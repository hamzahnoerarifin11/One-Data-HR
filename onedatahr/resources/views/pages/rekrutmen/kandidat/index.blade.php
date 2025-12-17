@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Kandidat</h3>
        <a href="{{ route('rekrutmen.kandidat.create') }}" class="btn btn-primary">Tambah Kandidat</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Tgl Melamar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kandidats as $k)
            <tr>
                <td>{{ $k->nama }}</td>
                <td>{{ $k->posisi->nama_posisi ?? '-' }}</td>
                <td>{{ $k->tanggal_melamar ? $k->tanggal_melamar->format('Y-m-d') : '-' }}</td>
                <td>
                    <a href="{{ route('rekrutmen.kandidat.show', $k->id_kandidat) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('rekrutmen.kandidat.edit', $k->id_kandidat) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('rekrutmen.kandidat.destroy', $k->id_kandidat) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus kandidat ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $kandidats->links() }}
</div>
@endsection
