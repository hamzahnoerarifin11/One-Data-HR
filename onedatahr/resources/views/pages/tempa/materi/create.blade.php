@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Upload Materi TEMPA</h1>
    </div>

    <form action="{{ route('tempa.materi.store') }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        @csrf
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">Judul Materi</label>
                <input type="text" name="judul" value="{{ old('judul') }}" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('judul') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="mb-2.5 block text-sm font-medium text-gray-900 dark:text-white">File Materi</label>
                <input type="file" name="file_materi" accept=".pdf,.doc,.docx,.ppt,.pptx" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-500 focus:outline-hidden focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" required>
                @error('file_materi') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">Format yang didukung: PDF, DOC, DOCX, PPT, PPTX. Maksimal 10MB.</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                Upload
            </button>
        </div>
    </form>
</div>
@endsection
