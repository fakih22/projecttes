@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Tambah Kategori</h1>

<form action="{{ route('category.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="nama" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('category.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
