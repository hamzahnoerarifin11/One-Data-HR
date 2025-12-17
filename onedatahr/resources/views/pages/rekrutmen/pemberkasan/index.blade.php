@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Pemberkasan</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Kandidat</th>
                <th>Posisi</th>
                <th>Selesai Recruitment</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $d)
            <tr>
                <td>{{ $d->kandidat->nama ?? '-' }}</td>
                <td>{{ $d->kandidat->posisi->nama_posisi ?? '-' }}</td>
                <td>{{ $d->selesai_recruitment ? $d->selesai_recruitment->format('Y-m-d') : '-' }}</td>
                <td><a href="{{ route('rekrutmen.pemberkasan.edit', $d->id_pemberkasan) }}" class="btn btn-sm btn-warning">Edit</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $data->links() }}
</div>
@endsection
