<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Dashboard KPI Performance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <style>
        body, div, table, tr, td, th { transition: background-color 0.3s ease, color 0.3s ease; }
    </style>
</head>
{{-- UBAH: p-6 menjadi p-4 sm:p-6 agar tidak terlalu lebar di HP --}}
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-4 sm:p-6">

    <div class="max-w-7xl mx-auto">
        {{-- TOMBOL KEMBALI --}}
        <div class="mb-4">
            <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium text-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Dashboard Utama</span>
            </a>
        </div>

        {{-- HEADER & FILTER --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Performance Dashboard</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Monitoring Penilaian Kinerja Karyawan</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                {{-- Toggle Dark Mode --}}
                <div class="flex justify-end sm:block">
                    <button id="theme-toggle" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 px-3 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition h-full">
                        <i id="theme-toggle-light-icon" class="fas fa-sun hidden"></i>
                        <i id="theme-toggle-dark-icon" class="fas fa-moon hidden"></i>
                    </button>
                </div>

                <form action="{{ route('kpi.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 w-full">
                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 w-full" 
                            placeholder="Cari Nama / Jabatan...">
                    </div>

                    {{-- Filter Tahun --}}
                    <div class="w-full sm:w-auto">
                        <select name="tahun" onchange="this.form.submit()" class="w-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                            @for($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Periode: {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Tombol Reset Search --}}
                    @if(request('search'))
                        <div class="self-end sm:self-center">
                            <a href="{{ route('kpi.index', ['tahun' => $tahun]) }}" class="text-red-500 hover:text-red-700 text-sm font-medium whitespace-nowrap">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- STATS CARDS (Grid responsive sudah bagus di kode asli) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            {{-- Card 1 --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-medium">Total Karyawan</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_karyawan'] }}</h3>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 p-2 sm:p-3 rounded-full text-blue-600 dark:text-blue-300"><i class="fas fa-users"></i></div>
                </div>
            </div>
            {{-- Card 2 --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-medium">Selesai (Final)</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['sudah_final'] }}</h3>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 p-2 sm:p-3 rounded-full text-green-600 dark:text-green-300"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            {{-- Card 3 --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-medium">Proses / Draft</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['draft'] }}</h3>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-2 sm:p-3 rounded-full text-yellow-600 dark:text-yellow-300"><i class="fas fa-edit"></i></div>
                </div>
            </div>
            {{-- Card 4 --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 sm:p-6 border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-medium">Rata-rata Skor</p>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['rata_rata'], 2) }}</h3>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900 p-2 sm:p-3 rounded-full text-purple-600 dark:text-purple-300"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        {{-- ALERT SECTION --}}
        <div class="mb-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-2" role="alert">
                    <strong class="font-bold"><i class="fas fa-check-circle"></i> Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                    <strong class="font-bold"><i class="fas fa-exclamation-triangle"></i> Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        {{-- CONTAINER DATA --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Daftar Status Karyawan ({{ $tahun }})</h3>
            </div>

            {{-- 
                ============================================
                TAMPILAN MOBILE (CARD VIEW)
                Muncul di layar kecil (md:hidden)
                ============================================
            --}}
            <div class="block md:hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($karyawanList as $index => $kry)
                        @php $kpi = $kry->kpiAssessment; @endphp
                        <div class="p-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            {{-- Header Card: Nama & Jabatan --}}
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white text-base">{{ $kry->Nama_Lengkap_Sesuai_Ijazah }}</div>
                                    <div class="text-xs text-gray-500">{{ $kry->pekerjaan->first()->Jabatan ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">NIK: {{ $kry->NIK ?? '-' }}</div>
                                </div>
                                <div class="text-xs text-gray-400 font-mono">#{{ $index + 1 }}</div>
                            </div>

                            {{-- Body Card: Status & Grade --}}
                            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg mb-3">
                                <div class="text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Status</span>
                                    @if($kpi)
                                        @if($kpi->status == 'FINAL')
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-0.5 rounded border border-green-400">FINAL</span>
                                        @elseif($kpi->status == 'SUBMITTED')
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded border border-yellow-400">SUBMITTED</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded border border-blue-400">DRAFT</span>
                                        @endif
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs px-2 py-0.5 rounded border border-gray-300">Belum Ada</span>
                                    @endif
                                </div>
                                <div class="text-center border-l border-gray-300 dark:border-gray-600 pl-4">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Skor Akhir</span>
                                    @if($kpi && $kpi->total_skor_akhir > 0)
                                        <div class="font-bold text-gray-900 dark:text-white">{{ number_format($kpi->total_skor_akhir, 2) }}</div>
                                        <div class="text-xs {{ $kpi->grade == 'Great' ? 'text-green-600' : ($kpi->grade == 'Good' ? 'text-blue-600' : ($kpi->grade == 'Average' ? 'text-yellow-600' : 'text-red-600')) }} font-bold">
                                            {{ $kpi->grade }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Card: Aksi --}}
                            <div class="flex items-center justify-end gap-2">
                                @if($kpi)
                                    <a href="{{ route('kpi.show', ['karyawan_id' => $kry->id_karyawan, 'tahun' => $tahun]) }}" 
                                       class="flex-1 text-center font-medium text-blue-600 dark:text-blue-500 border border-blue-500 px-3 py-2 rounded text-sm hover:bg-blue-50 dark:hover:bg-gray-700 transition">
                                        <i class="fas fa-edit"></i> Buka KPI
                                    </a>
                                    <form action="{{ route('kpi.destroy', $kpi->id_kpi_assessment) }}" method="POST" onsubmit="return confirm('Hapus data KPI ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 p-2 border border-red-200 dark:border-red-900/50 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('kpi.store') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="karyawan_id" value="{{ $kry->id_karyawan }}">
                                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                                        <button type="submit" class="w-full justify-center font-medium text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-sm flex items-center gap-2 transition shadow">
                                            <i class="fas fa-plus-circle"></i> Buat KPI Baru
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                            <p>Tidak ada data ditemukan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 
                ============================================
                TAMPILAN DESKTOP (TABEL BIASA)
                Disembunyikan di mobile (hidden md:block)
                ============================================
            --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 uppercase text-xs">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16 text-center">No</th>
                            <th scope="col" class="px-6 py-4">Nama Karyawan</th>
                            <th scope="col" class="px-6 py-4">Jabatan</th>
                            <th scope="col" class="px-6 py-4 text-center">Periode</th>
                            <th scope="col" class="px-6 py-4 text-center">Status</th>
                            <th scope="col" class="px-6 py-4 text-center">Skor & Grade</th>
                            <th scope="col" class="px-6 py-4 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($karyawanList as $index => $kry)
                        @php $kpi = $kry->kpiAssessment; @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            <td class="px-6 py-4 text-center font-medium text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                <div class="text-base font-semibold">{{ $kry->Nama_Lengkap_Sesuai_Ijazah }}</div>
                                <div class="font-normal text-gray-500 text-xs">{{ $kry->NIK ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $kry->pekerjaan->first()->Jabatan ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ $tahun }}</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($kpi)
                                    @if($kpi->status == 'FINAL')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300 border border-green-400">FINAL</span>
                                    @elseif($kpi->status == 'SUBMITTED')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300 border border-yellow-400">SUBMITTED</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 border border-blue-400">DRAFT</span>
                                    @endif
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300 border border-gray-500">Belum Ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($kpi && $kpi->total_skor_akhir > 0)
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($kpi->total_skor_akhir, 2) }}</span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full mt-1 whitespace-nowrap
                                            {{ $kpi->grade == 'Great' ? 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-900 dark:text-green-300' : '' }}
                                            {{ $kpi->grade == 'Good' ? 'bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                            {{ $kpi->grade == 'Average' ? 'bg-yellow-100 text-yellow-700 border border-yellow-300 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                            {{ $kpi->grade == 'Need Improvement' ? 'bg-red-100 text-red-700 border border-red-300 dark:bg-red-900 dark:text-red-300' : '' }}">
                                            {{ $kpi->grade ?? '-' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($kpi)
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('kpi.show', ['karyawan_id' => $kry->id_karyawan, 'tahun' => $tahun]) }}" 
                                           class="font-medium text-blue-600 dark:text-blue-500 hover:underline border border-blue-500 px-3 py-1 rounded hover:bg-blue-50 dark:hover:bg-gray-700 transition text-xs">
                                            <i class="fas fa-edit"></i> Buka
                                        </a>
                                        <form action="{{ route('kpi.destroy', $kpi->id_kpi_assessment) }}" method="POST" onsubmit="return confirm('Yakin ingin mereset/menghapus data KPI ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition" title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('kpi.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="karyawan_id" value="{{ $kry->id_karyawan }}">
                                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                                        <button type="submit" class="font-medium text-blue-600 dark:text-blue-500 hover:underline flex items-center gap-1 mx-auto">
                                            <i class="fas fa-plus-circle"></i> Buat
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-search text-4xl mb-3 text-gray-300"></i>
                                    <p>Tidak ada data karyawan ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPT DARK MODE (Sama seperti sebelumnya) --}}
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const htmlElement = document.documentElement;

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlElement.classList.add('dark');
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            htmlElement.classList.remove('dark');
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    htmlElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    htmlElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (htmlElement.classList.contains('dark')) {
                    htmlElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    htmlElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>