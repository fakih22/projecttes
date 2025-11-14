@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Edit Donat</h1>

<form action="{{ route('donat.update', $donat->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Nama Donat</label>
        <input type="text" name="nama" class="form-control" value="{{ $donat->nama }}" required>
    </div>

    <div class="form-group">
        <label>Kategori</label>
        <select name="category_id" class="form-control" required>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $donat->category_id == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" value="{{ $donat->harga }}" required>
    </div>

    <div class="form-group">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" value="{{ $donat->stok }}" required>
    </div>

    <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3">{{ $donat->deskripsi }}</textarea>
    </div>

    <div class="form-group">
        <label>Gambar (biarkan kosong jika tidak diganti)</label>
        <input type="file" name="gambar" class="form-control">
        @if($donat->gambar)
            <small>Gambar saat ini:</small><br>
            <img src="{{ asset('storage/'.$donat->gambar) }}" width="100">
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('donat.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@endsection
