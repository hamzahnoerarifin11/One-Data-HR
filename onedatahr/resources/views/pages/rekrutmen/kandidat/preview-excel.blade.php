@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">
        Preview Excel - {{ $kandidat->nama }}
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border text-sm">
            @foreach($sheet as $row)
                <tr>
                    @foreach($row as $cell)
                        <td class="border px-2 py-1">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
