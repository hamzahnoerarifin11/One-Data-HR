<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <title>Dashboard KPI Performance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <style>
        body, div, table, tr, td, th { transition: background-color 0.3s ease, color 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-6">

    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Performance Dashboard</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Monitoring Penilaian Kinerja Karyawan</p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center gap-4">
                <button id="theme-toggle" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <i id="theme-toggle-light-icon" class="fas fa-sun hidden"></i>
                    <i id="theme-toggle-dark-icon" class="fas fa-moon hidden"></i>
                </button>

                <form action="{{ route('kpi.index') }}" method="GET" class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-400">Periode:</label>
                    <select name="tahun" onchange="this.form.submit()" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-md px-3 py-1.5 text-sm">
                        @for($y = date('Y'); $y >= 2023; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Karyawan</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_karyawan'] }}</h3>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full text-blue-600 dark:text-blue-300"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Selesai</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['sudah_final'] }}</h3>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full text-green-600 dark:text-green-300"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Proses</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['draft'] }}</h3>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full text-yellow-600 dark:text-yellow-300"><i class="fas fa-edit"></i></div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Rata-rata</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['rata_rata'], 1) }}</h3>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full text-purple-600 dark:text-purple-300"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Daftar Status Karyawan ({{ $tahun }})</h3>
            </div>

            <div class="overflow-x-auto">
                <div class="max-w-7xl mx-auto">
        
        <div class="mb-4">
            {{-- Alert Sukses --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Alert Error (Exception) --}}
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Alert Validasi (Input Salah) --}}
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Ada Kesalahan Input:</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="flex flex-col md:flex-row ...">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs font-semibold">
                        <tr>
                            <th class="p-4 w-10 text-center">No</th>
                            <th class="p-4">Nama Karyawan</th>
                            <th class="p-4">Jabatan</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Skor</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 dark:text-gray-300 text-sm divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($karyawanList as $index => $kry)
                        @php $kpi = $kry->kpiAssessment; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="p-4 text-center">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800 dark:text-gray-100">{{ $kry->Nama_Lengkap_Sesuai_Ijazah }}</div>
                            </td>
                            <td class="p-4">
                                {{ $kry->pekerjaan->first()->Jabatan ?? '-' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($kpi)
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-yellow-100 text-yellow-800">
                                        {{ $kpi->status }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600">Belum Ada</span>
                                @endif
                            </td>
                            <td class="p-4 text-center font-bold">
                                {{ $kpi ? $kpi->total_skor_akhir : '-' }}
                            </td>
                            
                            <td class="p-4 text-center">
                                @if($kpi)
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('kpi.show', ['karyawan_id' => $kry->id_karyawan, 'tahun' => $tahun]) }}" 
                                           class="text-blue-600 hover:text-blue-800 border border-blue-200 px-3 py-1 rounded hover:bg-blue-50 transition">
                                            <i class="fas fa-edit"></i> Buka
                                        </a>

                                        <form action="{{ route('kpi.destroy', $kpi->id_kpi_assessment) }}" method="POST" onsubmit="return confirm('Yakin reset data KPI ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded hover:bg-red-50 transition" title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('kpi.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="karyawan_id" value="{{ $kry->id_karyawan }}">
                                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                                        <button type="submit" class="text-gray-600 hover:text-blue-600 border border-gray-300 px-3 py-1 rounded hover:bg-gray-50 transition">
                                            <i class="fas fa-plus"></i> Buat
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="p-4 text-center">Data kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Script toggle dark mode (sama seperti sebelumnya)
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