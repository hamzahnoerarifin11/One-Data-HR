@extends('layouts.app')

@section('title','Daily Rekrutmen')

@section('content')
<div class="p-6">
    <h1 class="text-xl font-semibold mb-4">Kalender Rekrutmen (Daily)</h1>
    <p class="mb-4">Halaman ini telah dipindahkan ke <a href="{{ route('rekrutmen.calendar') }}" class="text-primary">Kalender Rekrutmen</a>. Klik link untuk melihat dan mengelola data harian per posisi.</p>
    <p class="text-sm text-gray-500">(This is a fallback page — use <code>/rekrutmen/calendar</code> to access the calendar UI.)</p>
</div>
@endsection
