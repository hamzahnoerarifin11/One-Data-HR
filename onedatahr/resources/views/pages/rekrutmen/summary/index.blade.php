@extends('layouts.app')
@section('title','Dashboard Funnel Rekrutmen')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-2xl font-semibold text-black dark:text-white">
            Funnel Rekrutmen
        </h2>

        <form method="GET">
            <select name="posisi_id"
                onchange="this.form.submit()"
                class="rounded border px-4 py-2">
                @foreach($posisi as $p)
                    <option value="{{ $p->id_posisi }}"
                        {{ $posisiId==$p->id_posisi?'selected':'' }}>
                        {{ $p->nama_posisi }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3 xl:grid-cols-6">
        @foreach($data as $label=>$value)
        <div class="rounded-2xl border border-stroke bg-white p-5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <span class="text-sm text-gray-500">{{ $label }}</span>
            <h4 class="mt-2 text-3xl font-bold text-black dark:text-white">
                {{ $value }}
            </h4>
        </div>
        @endforeach
    </div>

    <!-- FUNNEL BAR -->
    <div class="mt-10 rounded-2xl border border-stroke bg-white p-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <h3 class="mb-6 text-lg font-semibold">Progress Funnel</h3>

        <div class="space-y-4">
            @foreach($data as $label=>$value)
                @php
                    $percent = $pelamar>0 ? round(($value/$pelamar)*100) : 0;
                @endphp
                <div>
                    <div class="mb-1 flex justify-between text-sm">
                        <span>{{ $label }}</span>
                        <span>{{ $percent }}%</span>
                    </div>
                    <div class="h-3 w-full rounded bg-gray-200">
                        <div class="h-3 rounded bg-primary"
                             style="width:{{ $percent }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
