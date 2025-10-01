@extends('layouts.app')

@section('title', 'Detail Vendor')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="fw-bold mb-4">Detail Vendor: {{ $vendor->nama_vendor }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Nama:</strong> {{ $vendor->nama_vendor }}</p>
            <p><strong>Alamat:</strong> {{ $vendor->alamat ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $vendor->kontak_email ?? '-' }}</p>
            <p><strong>Telepon:</strong> {{ $vendor->kontak_telepon ?? '-' }}</p>
            <p><strong>Deskripsi:</strong> {{ $vendor->deskripsi ?? '-' }}</p>
            <p><strong>Status:</strong> <span class="badge {{ $vendor->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($vendor->status) }}</span></p>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>
@endsection