@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Kategori Donat</h1>

<a href="{{ route('category.create') }}" class="btn btn-primary mb-3">Tambah Kategori</a>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Aktivitas</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $category)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $category->nama }}</td>
            <td>{{ $category->deskripsi }}</td>
            <td>
                <div class="d-flex">
                    <a href="{{ route('category.edit', $category->id) }}" class="btn btn-sm btn-primary mr-2">Edit</a>
                    <form action="{{ route('category.destroy', $category->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
