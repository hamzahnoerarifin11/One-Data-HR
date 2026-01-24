<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KbiAssessment;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class KbiController extends Controller
{
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
        $karyawan = Karyawan::with(['atasan', 'pekerjaan'])->where('nik', $user->nik)->first();
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data Karyawan tidak ditemukan.');
        }

        // C. Cek Penilaian Diri Sendiri
        $selfAssessment = KbiAssessment::where('karyawan_id', $karyawan->id_karyawan)
            ->where('tipe_penilai', 'DIRI_SENDIRI')
            ->where('tahun', $tahun)
            ->first();

        // D. Logic Daftar Karyawan (Tabel Kanan / Bawahan)
        $query = Karyawan::query();

        // Filter: Jangan tampilkan diri sendiri
        $query->where('id_karyawan', '!=', $karyawan->id_karyawan);

        // --- [LOGIC BARU: FILTER KHUSUS MANAGER] ---
        // Cek Jabatan User Login
        $userJabatan = $karyawan->pekerjaan->first()?->Jabatan;
        $userDivisi  = $karyawan->pekerjaan->first()?->Divisi;

        // Jika jabatan mengandung kata "Manager" (case insensitive)
        // Sesuaikan dengan nama jabatan di database Anda
        // Check apakah user adalah Manager level (Manager, GM, atau General Manager)
        $isManager = stripos($userJabatan, 'Manager') !== false || stripos($userJabatan, 'GM') !== false;

        if ($isManager) {
            // Manager hanya melihat karyawan di DIVISI YANG SAMA
            if ($userDivisi) {
                $query->whereHas('pekerjaan', function ($q) use ($userDivisi) {
                    $q->where('Divisi', $userDivisi);

                    // Opsional: Jika ingin filter level jabatan di bawahnya secara spesifik
                    // $q->whereIn('Jabatan', ['Supervisor', 'Staff', 'Officer']); 
                    // Atau gunakan logika NOT LIKE Manager jika ingin mengecualikan sesama manager
                    // $q->where('Jabatan', 'NOT LIKE', '%Manager%');
                });
            } else {
                // Jika data divisi manager kosong, mungkin tidak tampilkan apa-apa atau tampilkan semua (safety net)
                \Log::warning('Manager tanpa Divisi login: ' . $karyawan->Nama_Lengkap_Sesuai_Ijazah);
            }
        }

        // Filter Search (Tetap ada)
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('Nama_Sesuai_KTP', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Eksekusi Pagination
        $bawahanList = $query->paginate(10)->onEachSide(1)->appends(['tahun' => $tahun]);

        // Cek Status Penilaian (Looping)
        $bawahanList->through(function ($staff) use ($tahun, $user) {
            $staff->sudah_dinilai = KbiAssessment::where('karyawan_id', $staff->id_karyawan)
                ->where('penilai_id', $user->id)
                ->where('tipe_penilai', 'ATASAN') // Manager menilai Bawahan
                ->where('tahun', $tahun)
                ->exists();
            return $staff;
        });

        // E. Ambil Data Atasan (Kotak Kiri Bawah - Tetap Sama)
        $atasan = $karyawan->atasan;
        $sudahMenilaiAtasan = false;
        if ($atasan) {
            $sudahMenilaiAtasan = KbiAssessment::where('karyawan_id', $atasan->id_karyawan)
                ->where('penilai_id', $user->id)
                ->where('tipe_penilai', 'BAWAHAN')
                ->where('tahun', $tahun)
                ->exists();
        }

        // F. Logic Dropdown Pilih Atasan (Tetap Sama - Saya ringkas di sini agar tidak kepanjangan, 
        // tapi pastikan Anda copy paste bagian "Logika Baru Ambil List Semua Karyawan..." dari kode lama Anda)
        // ... (Masukkan kode $jabatanHierarchy dan logic $listCalonAtasan di sini) ...

        // --- SAYA SALIN ULANG BAGIAN PENTING HIERARKI AGAR ANDA BISA LANGSUNG COPY-PASTE UTUH ---
        $jabatanHierarchy = [
            'Direktur' => 1,
            'General Manager' => 2,
            'GM' => 2,
            'Manager' => 3,
            'Supervisor' => 4,
            'Staff' => 5,
            'Officer' => 6,
            'Assistant' => 7,
        ];
        $userLevel = 99;

        foreach ($jabatanHierarchy as $key => $level) {
            if (stripos($userJabatan, $key) !== false) {
                if ($level < $userLevel) {
                    $userLevel = $level;
                }
            }
        }

        if ($userLevel <= 1 || empty($userDivisi)) {
            $listCalonAtasan = collect();
        } else {
            $higherJabatan = [];
            foreach ($jabatanHierarchy as $jabatan => $level) {
                if ($level < $userLevel) $higherJabatan[] = $jabatan;
            }

            // Untuk GM (level 2), hanya tampilkan jabatan level 1 (Direktur Utama)
            if ($userLevel == 2) {
                $higherJabatan = array_filter($higherJabatan, function ($jabatan) use ($jabatanHierarchy) {
                    return ($jabatanHierarchy[$jabatan] ?? 99) == 1;
                });
            }
            // Ambil calon atasan dengan 2 kondisi:
            // 1. Jika level 1-2 (Direktur/GM): Bisa dari divisi manapun
            // 2. Jika level > 2 (Manager+): Utamakan divisi yang sama, tapi bisa juga dari level yang lebih tinggi
            $listCalonAtasan = Karyawan::where('id_karyawan', '!=', $karyawan->id_karyawan)
                ->whereHas('pekerjaan', function ($q) use ($userDivisi, $higherJabatan, $userLevel) {
                    if (!empty($higherJabatan)) {
                        $q->where(function ($subQ) use ($higherJabatan, $userDivisi, $userLevel) {
                            // Direktur/GM (level 1-2): Bisa dari divisi apapun
                            foreach ($higherJabatan as $jab) {
                                if (stripos($jab, 'Direktur') !== false || stripos($jab, 'GM') !== false) {
                                    $subQ->orWhere('Jabatan', 'LIKE', '%' . $jab . '%');
                                } else {
                                    // Jabatan lain: hanya dari divisi yang sama
                                    $subQ->orWhere(function ($sq) use ($jab, $userDivisi) {
                                        $sq->where('Jabatan', 'LIKE', '%' . $jab . '%')
                                            ->where('Divisi', $userDivisi);
                                    });
                                }
                            }
                        });
                    } else {
                        $q->where('Jabatan', 'NONEXISTENT');
                    }
                })->orderBy('Nama_Lengkap_Sesuai_Ijazah', 'ASC')->get();
        }
        // -----------------------------------------------------------------------------------------

        return view('pages.kbi.index', compact(
            'karyawan',
            'selfAssessment',
            'bawahanList',
            'atasan',
            'sudahMenilaiAtasan',
            'listCalonAtasan',
            'tahun'
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
                    $jabatanUser = $karyawanUser->pekerjaan->first()?->Jabatan ?? '';
                    $jabatanLower = strtolower($jabatanUser);

                    // Jika GM atau General Manager, tampilkan semua karyawan di divisi yang sama
                    if (strpos($jabatanLower, 'general manager') !== false || strpos($jabatanLower, 'GM') !== false) {
                        $divisiUser = $karyawanUser->pekerjaan->first()?->Divisi ?? '';
                        $query->whereHas('pekerjaan', function ($q) use ($divisiUser) {
                            $q->where('Divisi', $divisiUser);
                        });
                    } elseif (strpos($jabatanLower, 'manager') !== false) {
                        // Jika manager (tapi bukan general manager), tampilkan bawahan langsung
                        $query->where('atasan_id', $karyawanUser->id_karyawan);
                    } else {
                        // Untuk jabatan lain dengan role manager, mungkin tampilkan bawahan
                        $query->where('atasan_id', $karyawanUser->id_karyawan);
                    }
                }
            }
        }
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('Nama_Lengkap_Sesuai_Ijazah', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('NIK', 'LIKE', '%' . $keyword . '%');
            });
        }

        // 3. Filter Jabatan (PERBAIKAN DISINI)
        if ($request->has('jabatan') && $request->jabatan != '') {
            $query->whereHas('pekerjaan', function ($q) use ($request) {
                // Ubah 'nama_jabatan' jadi 'Jabatan'
                $q->where('Jabatan', $request->jabatan); // <--- UBAH INI
            });
        }

        $rawList = $query->get();
        $userMap = \App\Models\User::whereNotNull('nik')->pluck('id', 'nik')->toArray();

        // 4. Mapping Status (Sama seperti sebelumnya)
        $listKaryawan = $rawList->map(function ($kry) use ($tahun, $userMap) {
            $kry->status_diri = KbiAssessment::where('karyawan_id', $kry->id_karyawan)
                ->where('tipe_penilai', 'DIRI_SENDIRI')
                ->where('tahun', $tahun)
                ->exists();

            if ($kry->atasan_id) {
                $penilaiUserId = $userMap[$kry->NIK] ?? 0;
                if ($penilaiUserId > 0) {
                    $sudahNilaiAtasan = KbiAssessment::where('karyawan_id', $kry->atasan_id)
                        ->where('penilai_id', $penilaiUserId) // Sesuaikan logic user_id
                        ->where('tipe_penilai', 'BAWAHAN')
                        ->where('tahun', $tahun)
                        ->exists();
                    $kry->status_atasan = $sudahNilaiAtasan ? 'DONE' : 'PENDING';
                } else {
                    $kry->status_atasan = 'PENDING';
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
        // Ubah 'nama_jabatan' jadi 'Jabatan'
        $listJabatan = \App\Models\Pekerjaan::distinct()
            ->pluck('Jabatan') // <--- UBAH INI
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
