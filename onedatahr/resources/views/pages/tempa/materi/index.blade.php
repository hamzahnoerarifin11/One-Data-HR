@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Materi TEMPA</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Daftar materi TEMPA</p>
        </div>
        @can('createTempaMateri')
        <a href="{{ route('tempa.materi.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
            Upload Materi
        </a>
        @endcan
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full min-w-full border-collapse">
                <thead>
                    <tr class="border-y border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">#</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Judul</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Uploaded By</th>
                        <th class="px-6 py-3 text-left text-md font-medium text-gray-600 dark:text-gray-400">Tanggal Upload</th>
                        <th class="px-6 py-3 text-right text-md font-medium text-gray-600 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materis as $index => $materi)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                        <td class="px-6 py-4 text-md text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-md font-medium text-gray-900 dark:text-white">{{ $materi->judul }}</td>
                        <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $materi->uploader->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-md text-gray-600 dark:text-gray-300">{{ $materi->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($materi->id_materi)
                                <a href="{{ route('tempa.materi.download', $materi->id_materi) }}">
                                    Download
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
