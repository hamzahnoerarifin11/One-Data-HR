<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KbiAssessment;
use App\Models\Karyawan;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class KbiController extends Controller
{
    // ==========================================================
    // HELPER: HIERARKI JABATAN
    // ==========================================================
    private function getJabatanHierarchy()
    {
        // URUTKAN DARI YANG TERPANJANG UNTUK MENGHINDARI PARTIAL MATCH YANG SALAH
        // Contoh: "Senior Manager" harus dicek sebelum "Manager"
        return [
            'Direktur' => 1,
            'Director' => 1,
            'General Manager' => 2,
            'Senior Manager' => 2,
            'senior_manager' => 2,
            'Head' => 3,
            'Manajer' => 3,
            'Manager' => 3,
            'Supervisor' => 4,
            'Spv' => 4,
            'Staff' => 5,
            'Staf' => 5,
            'Officer' => 6,
            'Assistant' => 7,
        ];
    }

    private function getLevel($jabatan)
    {
        $hierarchy = $this->getJabatanHierarchy();
        $level = 99; // Default Level (Unknown/Lowest)
        if (!$jabatan) return $level;

        foreach ($hierarchy as $key => $lvl) {
            if (stripos($jabatan, $key) !== false) {
                // Return immediately on first match because we sorted by length/priority
                // OR Keep logic to find lowest number (highest rank)
                if ($lvl < $level) {
                    $level = $lvl;
                }
            }
        }
        return $level;
    }

    // ==========================================================
    // 1. HALAMAN UTAMA (INDEX) - DENGAN SEARCH & PAGINATION
    // ==========================================================
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->input('tahun', date('Y'));

        // A. Validasi NIK User Login
        if (empty($user->nik)) {
            return redirect()->back()->with('error', 'Akun Login tidak memiliki NIK.');
        }

        // B. Cari Data Karyawan (Diri Sendiri)
        // Eager load necessary relations
        $karyawan = Karyawan::with(['atasan', 'pekerjaan.position', 'pekerjaan.company', 'pekerjaan.division'])
            ->where('nik', $user->nik)->first();

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan.');
        }

        // C. Cek Penilaian Diri Sendiri
        $selfAssessment = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tipe_penilai', 'DIRI_SENDIRI')
            ->where('tahun', $tahun)
            ->first();

        // --- [LOGIC BARU: FILTER LIST TIM] ---
        // 1. Tentukan Level User
        $userPekerjaan = $karyawan->pekerjaan->first();
        // Fallback: Position Name -> Jabatan Column -> Empty String
        $userJabatan = $userPekerjaan?->level?->name ?? '';
        $userDivisionId = $userPekerjaan?->division_id;

        // Calculate Level safely
        $userLevel = $this->getLevel($userJabatan);

        // 2. Ambil Semua Karyawan di Divisi yang Sama (Kecuali Diri Sendiri)
        $karyawanCollection = collect();

        if ($userDivisionId) {
            $query = Karyawan::with(['pekerjaan.position', 'pekerjaan.company', 'pekerjaan.division'])
                ->where('id_karyawan', '!=', $karyawan->id_karyawan)
                ->whereHas('pekerjaan', function ($q) use ($userDivisionId) {
                    $q->where('division_id', $userDivisionId);
                });

            // Filter Search
            if ($request->has('search') && $request->search != '') {
                $keyword = $request->search;
                $query->where(function ($q) use ($keyword) {
                    $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('Nama_Sesuai_KTP', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
                });
            }

            // Filter Company
            if ($request->has('filter_company') && $request->filter_company != '') {
                $query->whereHas('pekerjaan', function ($q) use ($request) {
                    $q->whereHas('company', function ($companyQ) use ($request) {
                        $companyQ->where('name', $request->filter_company);
                    });
                });
            }

            $rawList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->get();
            // $candidates = $queryAtasan->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->get();

            // 3. Filter Level: User tidak boleh melihat atasan (Level < UserLevel)
            $karyawanCollection = $rawList->filter(function ($staff) use ($userLevel) {
                // Robust fetch job title: PRIORITASKAN LEVEL RESMI DARI DATABASE!
                $staffJab = $staff->pekerjaan->first()?->level?->name 
                    ?? $staff->pekerjaan->first()?->position?->name 
                    ?? $staff->pekerjaan->first()?->Jabatan 
                    ?? '';

                if (empty($staffJab)) return false; // Safety check

                $staffLvl = $this->getLevel($staffJab);
                // StaffLevel >= UserLevel (Bawahan atau setara) - Angka besar = Level rendah
                return $staffLvl >= $userLevel;
            });
        }

        // 4. Pagination Manual
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $results = $karyawanCollection->slice(($page - 1) * $perPage, $perPage)->values();

        $bawahanList = new LengthAwarePaginator($results, $karyawanCollection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        // 5. Cek Status Penilaian DAN Inject Logic Assessment
        $bawahanList->through(function ($staff) use ($tahun, $user, $userLevel) {
            // A. Status Penilaian
            $staff->sudah_dinilai = KbiAssessment::where('karyawan_id', $staff->id_karyawan)
                ->where('penilai_id', $user->id)
                ->where('tipe_penilai', 'ATASAN')
                ->where('tahun', $tahun)
                ->exists();

            // B. Logic Can Assess (Bisa Menilai?)
            $staffJab = $staff->pekerjaan->first()?->level?->name ?? '';
            $staffLevel = $this->getLevel($staffJab);

            $staff->calculated_level = $staffLevel;
            $staff->can_assess = false;
            $staff->lock_reason = '';

            if ($userLevel != 99) {
                // Rule: Hanya bisa menilai 1 level di bawahnya (UserLevel + 1)
                if ($staffLevel == $userLevel + 1) {
                    $staff->can_assess = true;
                } else {
                    $staff->can_assess = false;
                    if ($staffLevel <= $userLevel) {
                        $staff->lock_reason = 'Level setara/lebih tinggi';
                    } else {
                        $staff->lock_reason = 'Hanya bisa menilai 1 level di bawah';
                    }
                }
            } else {
                $staff->lock_reason = 'Level user tidak dikenali';
            }

            return $staff;
        });

        // E. Ambil Data Atasan (Kotak Kiri Bawah)
        $atasan = $karyawan->atasan;
        $sudahMenilaiAtasan = false;
        if ($atasan) {
            $sudahMenilaiAtasan = KbiAssessment::where('karyawan_id', $atasan->id_karyawan)
                ->where('penilai_id', $user->id)
                ->where('tipe_penilai', 'BAWAHAN')
                ->where('tahun', $tahun)
                ->exists();
        }

        // F. Logic Dropdown Pilih Atasan (TEPAT 1 LEVEL DI ATAS USER)
        $listCalonAtasan = collect();
        if ($userLevel > 1) {
            // Default target: 1 level diatasuser
            $targetLevel = $userLevel - 1;
            
            // Query dasar
            $queryAtasan = Karyawan::with(['pekerjaan.position', 'pekerjaan.division'])
                ->where('id_karyawan', '!=', $karyawan->id_karyawan);

            // LOGIC KHUSUS: MANAGER (Level 3) & GM/SENIOR MANAGER (Level 2)
            // Atasan mereka adalah Direktur (Level 1) & Tidak dibatasi Divisi
            if ($userLevel == 2 || $userLevel == 3) {
                $targetLevel = 1;
                // Tidak ada filter divisi (Global)
            } else {
                // Selain itu (Staff, SPV, dll), harus dalam satu divisi
                if ($userDivisionId) {
                    $queryAtasan->whereHas('pekerjaan', function ($q) use ($userDivisionId) {
                        $q->where('division_id', $userDivisionId);
                    });
                }
            }

            // Ambil kandidat, lalu lakukan penyaringan yang ketat di PHP berdasarkan level
            $candidates = $queryAtasan->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->get();

            $listCalonAtasan = $candidates->filter(function ($calon) use ($targetLevel) {
                // PRIORITAS UTAMA: Ambil Level Name resmi dari tabel levels (agar 'Staff' di jabatan manual tidak override 'Supervisor' di level)
                $calonJab = $calon->pekerjaan->first()?->level?->name 
                    ?? $calon->pekerjaan->first()?->position?->name 
                    ?? $calon->pekerjaan->first()?->Jabatan 
                    ?? '';
                
                $calonLvl = $this->getLevel($calonJab);
                return $calonLvl === $targetLevel;
            })->values();
        }

        // List Companies Dropdown
        $listCompanies = \App\Models\Company::distinct()->orderBy('name')->pluck('name');

        return view('pages.kbi.index', compact(
            'karyawan',
            'selfAssessment',
            'bawahanList',
            'atasan',
            'sudahMenilaiAtasan',
            'listCalonAtasan',
            'tahun',
            'listCompanies',
            'userLevel',
            'userJabatan'
        ));
    }

    // ==========================================================
    // 2. HALAMAN FORMULIR (CREATE)
    // ==========================================================
    public function create(Request $request)
    {
        $targetId = $request->karyawan_id;
        $tipe = $request->tipe;

        if (!$targetId || !$tipe) {
            return redirect()->route('kbi.index')->with('error', 'Data tidak lengkap.');
        }

        $targetKaryawan = Karyawan::with('pekerjaan')->where('id_karyawan', $targetId)->first();

        if (!$targetKaryawan) {
            return redirect()->route('kbi.index')->with('error', 'Karyawan tidak ditemukan.');
        }

        // Ambil Soal
        $daftarSoal = $this->getDaftarPertanyaan();

        return view('pages.kbi.create', compact('targetKaryawan', 'tipe', 'daftarSoal'));
    }

    // ==========================================================
    // 3. SIMPAN DATA (STORE)
    // ==========================================================
    public function store(Request $request)
    {
        $user = Auth::user();
        $tahun = date('Y');

        $request->validate([
            'skor' => 'required|array',
            'karyawan_id' => 'required',
            'tipe_penilai' => 'required',
        ]);

        $skorInput = $request->skor;
        $totalSkor = array_sum($skorInput);
        $jumlahSoal = count($skorInput);

        $rataRata = $jumlahSoal > 0 ? round($totalSkor / $jumlahSoal, 2) : 0;

        KbiAssessment::updateOrCreate(
            [
                'karyawan_id' => $request->karyawan_id,
                'penilai_id' => $user->id,
                'tipe_penilai' => $request->tipe_penilai,
                'tahun' => $tahun,
            ],
            [
                'rata_rata_akhir' => $rataRata,
                'status' => 'FINAL',
                'tanggal_penilaian' => now(),
            ]
        );

        return redirect()->route('kbi.index')->with('success', 'Penilaian KBI berhasil disimpan!');
    }

    // ==========================================================
    // 4. DATABASE SOAL (HARDCODE)
    // ==========================================================
    private function getDaftarPertanyaan()
    {
        return [
            [
                'kategori' => 'KOMUNIKATIF',
                'soal' => [
                    1 => 'Sopan dan santun dalam berkomunikasi serta menghargai perbedaan.',
                    2 => 'Menyampaikan informasi secara sistematis, akurat, dan mudah dipahami.',
                    3 => 'Mudah bersinergi dan berkolaborasi baik dengan sesama tim, lintas fungsi, maupun pihak eksternal.',
                ]
            ],
            [
                'kategori' => 'UNGGUL',
                'soal' => [
                    4 => 'Menetapkan standar kinerja tinggi dan konsisten mencapainya.',
                    5 => 'Selalu mencari solusi inovatif dalam bekerja.',
                    6 => 'Berkontribusi pada pencapaian kinerja yang lebih tinggi dibanding standar.',
                ]
            ],
            [
                'kategori' => 'AGAMIS',
                'soal' => [
                    7 => 'Menjalankan nilai spiritual dalam bekerja secara konsisten.',
                    8 => 'Mengedepankan kejujuran dan keberkahan sebagai fondasi keputusan kerja.',
                    9 => 'Menjaga perilaku sesuai etika dan ajaran agama.',
                ]
            ],
            [
                'kategori' => 'TANGGUNG JAWAB',
                'soal' => [
                    10 => 'Menyelesaikan tugas dengan tepat waktu, sesuai target perusahaan.',
                    11 => 'Bertanggung jawab atas dampak kualitas produk dan layanan ke pelanggan.',
                    12 => 'Berani mengakui kesalahan dan mengambil inisiatif perbaikan.',
                ]
            ],
            [
                'kategori' => 'MANFAAT',
                'soal' => [
                    13 => 'Selalu mempertimbangkan nilai tambah produk atau proses bagi pelanggan atau pengguna akhir.',
                    14 => 'Berkontribusi terhadap proyek atau program yang berdampak luas.',
                    15 => 'Menghindari aktivitas yang tidak berdampak pada efisiensi atau kualitas kerja.',
                ]
            ],
            [
                'kategori' => 'EMPATI',
                'soal' => [
                    16 => 'Tanggap terhadap kebutuhan rekan kerja maupun customer.',
                    17 => 'Bersedia membantu saat ada anggota tim atau mitra dalam kesulitan.',
                    18 => 'Menunjukkan kepedulian dalam komunikasi dan tindakan sehari-hari.',
                ]
            ],
            [
                'kategori' => 'MORAL',
                'soal' => [
                    19 => 'Memperlakukan semua orang secara adil tanpa memandang jabatan atau latar belakang.',
                    20 => 'Menolak segala bentuk perilaku tidak etis, manipulatif, atau diskriminatif.',
                    21 => 'Menjunjung tinggi integritas dalam pengambilan keputusan.',
                ]
            ],
            [
                'kategori' => 'BELAJAR',
                'soal' => [
                    22 => 'Proaktif mencari ilmu, teknologi, dan best practice terbaru untuk pekerjaan maupun tentang industri global.',
                    23 => 'Menerapkan pembelajaran dalam proyek nyata untuk peningkatan berkelanjutan.',
                    24 => 'Berbagi pengetahuan dan keahlian ke anggota tim.',
                ]
            ],
            [
                'kategori' => 'AMANAH',
                'soal' => [
                    25 => 'Menjaga kerahasiaan data, dokumen, dan keputusan strategis dalam lingkup divisi maupun perusahaan.',
                    26 => 'Menjalankan amanah dari atasan, pelanggan, dan mitra tanpa penyimpangan.',
                    27 => 'Dipercaya untuk menangani pekerjaan penting atau sensitif.',
                ]
            ],
            [
                'kategori' => 'JUJUR',
                'soal' => [
                    28 => 'Tidak memanipulasi data, laporan, atau hasil kerja.',
                    29 => 'Menyampaikan kendala yang dihadapi dengan transparan.',
                    30 => 'Konsisten antara ucapan dan tindakan, terutama dalam kerja tim.',
                ]
            ],
            [
                'kategori' => 'ANTUSIAS',
                'soal' => [
                    31 => 'Selalu terlihat bersemangat menghadapi tugas baru atau proyek lintas tim.',
                    32 => 'Menunjukkan energi positif meski dalam tekanan atau tantangan pekerjaan.',
                    33 => 'Menjadi penyemangat dan pendorong semangat tim.',
                ]
            ],
        ];
    }

    // [BARU] Fungsi untuk menyimpan pilihan atasan
    public function updateAtasan(Request $request)
    {
        $request->validate([
            'atasan_id' => 'required|exists:karyawan,id_karyawan',
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
        ]);

        $karyawan = Karyawan::find($request->karyawan_id);
        $karyawan->atasan_id = $request->atasan_id;
        $karyawan->save();

        return redirect()->back()->with('success', 'Data Atasan berhasil diperbarui! Silakan lanjut menilai.');
    }
    public function resetAtasan(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
        ]);

        $karyawan = Karyawan::find($request->karyawan_id);
        $karyawan->atasan_id = null;
        $karyawan->save();

        return redirect()->back()->with('success', 'Data Atasan berhasil direset! Silakan pilih atasan baru.');
    }

    public function monitoring(Request $request)
    {
        $tahun = request()->get('tahun', date('Y'));

        // 1. Query Dasar
        $query = Karyawan::with(['pekerjaan', 'atasan']);

        // Kecualikan user yang sedang login dari list monitoring
        $user = auth()->user();
        if ($user && $user->nik) {
            $query->where('NIK', '!=', $user->nik);
        }
        if ($user) {
            // Jika admin atau superadmin, tampilkan semua
            if ($user->hasRole(['admin', 'superadmin'])) {
                // No filter, show all
            } else {
                $karyawanUser = Karyawan::where('NIK', $user->nik)->first();
                if ($karyawanUser) {
                    $jabatanUser = $karyawanUser->pekerjaan->first()?->position?->name ?? '';
                    $jabatanLower = strtolower($jabatanUser);

                    // Jika senior_manager, General Manager, ATAU Manager, tampilkan semua karyawan di divisi yang sama
                    if (strpos($jabatanLower, 'general manager') !== false || 
                        strpos($jabatanLower, 'senior_manager') !== false || 
                        strpos($jabatanLower, 'manager') !== false) {
                        
                        $divisiUser = $karyawanUser->pekerjaan->first()?->division?->name ?? '';
                        $query->whereHas('pekerjaan', function ($q) use ($divisiUser) {
                            $q->whereHas('division', function ($divQ) use ($divisiUser) {
                                $divQ->where('name', $divisiUser);
                            });
                        });
                    } else {
                        // Untuk jabatan lain, tampilkan bawahan langsung
                        $query->where('atasan_id', $karyawanUser->id_karyawan);
                    }
                }
            }
        }
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('Nama_Sesuai_KTP', 'LIKE', '%' . $keyword . '%');
            });
        }

        // 3. Filter Jabatan (PERBAIKAN DISINI)
        if ($request->has('jabatan') && $request->jabatan != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                $q->whereHas('position', function ($posQ) use ($request) {
                    $posQ->where('name', $request->jabatan);
                });
            });
        }

        $rawList = $query->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->get();
        $userMap = \App\Models\User::whereNotNull('nik')->pluck('id', 'nik')->toArray();

        // 4. Mapping Status (Sama seperti sebelumnya)
        $listKaryawan = $rawList->map(function ($kry) use ($tahun, $userMap) {
            $kry->status_diri = KbiAssessment::where('karyawan_id', $kry->id_karyawan)
                ->where('tipe_penilai', 'DIRI_SENDIRI')
                ->where('tahun', $tahun)
                ->exists();

            if ($kry->atasan_id) {
                // LOGIC REVERT: Check if EMPLOYEE has rated ATASAN (Feedback ke Atasan)
                // This matches the Dashboard logic where "Feedback Atasan" means "Bawahan menilai Atasan"
                
                // 1. Get Employee's User ID (Penilai)
                $penilaiUserId = $userMap[$kry->NIK] ?? 0;

                if ($penilaiUserId > 0) {
                    $sudahNilaiAtasan = KbiAssessment::where('karyawan_id', $kry->atasan_id) // Target: Boss
                        ->where('penilai_id', $penilaiUserId) // Rater: Employee
                        ->where('tipe_penilai', 'BAWAHAN')
                        ->where('tahun', $tahun)
                        ->exists();
                    $kry->status_atasan = $sudahNilaiAtasan ? 'DONE' : 'PENDING';
                } else {
                    $kry->status_atasan = 'PENDING'; // Employee user not found
                }
            } else {
                $kry->status_atasan = 'NA';
            }

            $kry->is_complete = $kry->status_diri && ($kry->status_atasan == 'DONE' || $kry->status_atasan == 'NA');
            return $kry;
        });

        // 5. Filter Status (Sama seperti sebelumnya)
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'sudah') {
                $listKaryawan = $listKaryawan->where('is_complete', true);
            } elseif ($request->status == 'belum') {
                $listKaryawan = $listKaryawan->where('is_complete', false);
            }
        }

        // 6. List Jabatan untuk Dropdown (PERBAIKAN DISINI)
        $listJabatan = \App\Models\Position::distinct()
            ->pluck('name')
            ->filter()
            ->sort();

        $totalKaryawan = $listKaryawan->count();
        $sudahSelesaiSemua = $listKaryawan->where('is_complete', true)->count();
        $belumSelesai = $totalKaryawan - $sudahSelesaiSemua;
        // 7. PAGINASI MANUAL
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $results = $listKaryawan->slice(($page - 1) * $perPage, $perPage)->all();

        $paginatedKaryawan = new LengthAwarePaginator($results, count($listKaryawan), $perPage);
        $paginatedKaryawan->setPath($request->url());
        $paginatedKaryawan->appends($request->all());
        $paginatedKaryawan->onEachSide(1);

        return view('pages.kbi.monitoring', compact(
            'totalKaryawan',
            'sudahSelesaiSemua',
            'belumSelesai',
            'listJabatan'
        ) + [
            'listKaryawan' => $paginatedKaryawan,
            'tahun' => $tahun
        ]);
    }
}
