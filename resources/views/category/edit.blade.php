@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Edit Kategori</h1>

<form action="{{ route('category.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Nama Kategori</label>
        <input type="text" name="nama" class="form-control" value="{{ $category->nama }}" required>
    </div>


    <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3">{{ $category->deskripsi }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('category.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
