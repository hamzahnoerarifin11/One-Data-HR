@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            Input WIG Rekrutmen
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li><a class="font-medium" href="#">Dashboard /</a></li>
                <li class="font-medium text-primary">Form Input</li>
            </ol>
        </nav>
    </div>
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">
                Informasi Lowongan & Progress
            </h3>
        </div>

        <form method="POST" action="{{ route('wig-rekrutmen.store') }}" class="p-6.5">
            @csrf

            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">
                    Posisi <span class="text-meta-1">*</span>
                </label>
                <div class="relative z-20 bg-transparent dark:bg-form-input">
                    <select name="posisi_id" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 px-5 outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary">
                        <option value="" disabled selected>Pilih Posisi</option>
                        @foreach($posisi as $p)
                            <option value="{{ $p->id_posisi }}">{{ $p->nama_posisi }}</option>
                        @endforeach
                    </select>
                    <span class="absolute top-1/2 right-4 z-30 -translate-y-1/2">
                        <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z" fill=""></path>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach([
                    'fpk_user' => 'FPK User', 'fpk_hrd' => 'FPK HRD',
                    'fpk_finance' => 'FPK Finance', 'fpk_direktur' => 'FPK Direktur',
                    'tanggal_publish_loker' => 'Publish Loker', 'tanggal_tes_kompetensi' => 'Tes Kompetensi',
                    'tanggal_interview_hr' => 'Interview HR', 'tanggal_interview_user' => 'Interview User',
                    'tanggal_bg_checking' => 'Background Checking', 'tanggal_mulai_training' => 'Mulai Training',
                    'tanggal_selesai_training' => 'Selesai Training'
                ] as $name => $label)
                <div>
                    <label class="mb-2.5 block text-black dark:text-white">{{ $label }}</label>
                    <input type="date" name="{{ $name }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
                @endforeach
            </div>

            <hr class="my-8 border-stroke dark:border-strokedark" />

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach([
                    'total_pelamar' => 'Total Pelamar', 'total_lead' => 'Total Lead',
                    'total_lolos_psikotes' => 'Lolos Psikotes', 'dipanggil_tes_kompetensi' => 'Panggil Tes Komp.',
                    'hadir_tes_kompetensi' => 'Hadir Tes Komp.', 'lolos_tes_kompetensi' => 'Lolos Tes Komp.',
                    'dipanggil_interview_hr' => 'Panggil Int. HR', 'hadir_interview_hr' => 'Hadir Int. HR',
                    'lolos_interview_hr' => 'Lolos Int. HR', 'dipanggil_interview_user' => 'Panggil Int. User',
                    'hadir_interview_user' => 'Hadir Int. User', 'lolos_interview_user' => 'Lolos Int. User'
                ] as $name => $label)
                <div>
                    <label class="mb-2.5 block text-black dark:text-white">{{ $label }}</label>
                    <input type="number" name="{{ $name }}" value="0" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                </div>
                @endforeach
            </div>

            <div class="mb-6">
                <label class="mb-2.5 block text-black dark:text-white">Keterangan</label>
                <textarea name="keterangan" rows="4" placeholder="Masukkan catatan tambahan..." class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary"></textarea>
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90">
                Simpan Data Rekrutmen
            </button>
        </form>
    </div>
</div>
@endsection
