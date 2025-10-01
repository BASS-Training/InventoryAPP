@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="fw-bold mb-4">Edit Vendor: {{ $vendor->nama_vendor }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('vendors.update', $vendor) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label for="nama_vendor" class="form-label">Nama Vendor</label>
                    <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" value="{{ $vendor->nama_vendor }}" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat">{{ $vendor->alamat }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="kontak_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="kontak_email" name="kontak_email" value="{{ $vendor->kontak_email }}">
                </div>
                <div class="mb-3">
                    <label for="kontak_telepon" class="form-label">Telepon</label>
                    <input type="text" class="form-control" id="kontak_telepon" name="kontak_telepon" value="{{ $vendor->kontak_telepon }}">
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi">{{ $vendor->deskripsi }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif" {{ $vendor->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $vendor->status == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection