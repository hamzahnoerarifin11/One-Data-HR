@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Detail User
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informasi akun dan hak akses pengguna
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user() && auth()->user()->role === 'superadmin')
                <a href="{{ route('users.edit', $user->id) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-yellow-500 px-5 py-2.5
                          text-white font-medium hover:bg-yellow-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828
                                 l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif

            <a href="{{ route('users.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2
                      text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300
                      dark:hover:bg-white/[0.05] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Card --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-lg
                dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- Section: Informasi User --}}
        <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
            <span class="w-1 h-6 bg-brand-600 rounded-full"></span>
            Informasi User
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <div>
                <label class="text-sm text-gray-500">Nama Lengkap</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $user->name }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Email</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $user->email }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">NIK</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $user->nik ?? '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Jabatan</label>
                <div class="mt-1 font-semibold text-gray-900 dark:text-white">
                    {{ $user->jabatan ?? '-' }}
                </div>
            </div>

            <div>
                <label class="text-sm text-gray-500">Role</label>
                <div class="mt-1 font-semibold capitalize text-brand-600 dark:text-brand-400">
                    {{ $user->role }}
                </div>
            </div>

        </div>

        <!-- <hr class="my-8 border-gray-100 dark:border-gray-800" /> -->

        {{-- Footer --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800
                    flex justify-between items-center text-xs text-gray-500">
            <p>Dibuat Pada : {{ $user->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }}</p>
            <p>Terakhir Diperbarui : {{ $user->updated_at->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }}</p>
        </div>

    </div>
</div>
@endsection
