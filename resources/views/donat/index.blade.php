@extends('layouts.admin')

@section('main-content')
    <h1 class="h3 mb-4 text-gray-800">Daftar Donat</h1>

    <a href="{{ route('donat.create') }}" class="btn btn-primary mb-3">Tambah Donat</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Nama Donat</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($donats as $donat)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        @if ($donat->gambar)
                            <img src="{{ asset('storage/' . $donat->gambar) }}" alt="{{ $donat->nama }}" width="70" height="70"
                                style="object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>

                    <td>{{ $donat->nama }}</td>
                    <td>{{ $donat->category->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($donat->harga, 0, ',', '.') }}</td>
                    <td>{{ $donat->stok }}</td>
                    <td>
                        <div class="d-flex">
                            <a href="{{ route('donat.edit', $donat->id) }}" class="btn btn-sm btn-primary mr-2">Edit</a>
                            <form action="{{ route('donat.destroy', $donat->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin hapus donat ini?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection