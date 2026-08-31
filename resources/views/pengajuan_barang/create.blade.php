@extends('layouts.app')

@php
    $judul = ($tipe == 'peminjaman') ? 'Buat Peminjaman Aset' : 'Buat Permintaan Barang';
@endphp

@section('title', $judul)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>{{ $judul }}</h1>
                {{-- Nanti bisa ada link ke daftar pengajuan saya --}}
                {{-- <a href="{{ route('pengajuan.barang.index') }}" class="btn btn-secondary">Lihat Pengajuan Saya</a> --}}
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('pengajuan.barang.store') }}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="tipe_pengajuan" value="{{ $tipe }}">

                        <div class="alert alert-light border">
                            <strong>Pemohon:</strong> {{ Auth::user()->name }}<br>
                            <small class="text-muted">{{ Auth::user()->email }} · Tanggal pengajuan: {{ now()->isoFormat('DD MMMM YYYY') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Barang yang Diajukan <span class="text-danger">*</span></label>
                            <select class="form-select @error('barang_id') is-invalid @enderror" id="barang_id" name="barang_id" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }} data-stok="{{ $barang->stok }}">
                                        {{ $barang->nama_barang }} (Stok: {{ $barang->stok }} {{ $barang->unit->singkatan_unit ?? $barang->unit->nama_unit ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('barang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kuantitas_diminta" class="form-label">Kuantitas Diminta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('kuantitas_diminta') is-invalid @enderror" id="kuantitas_diminta" name="kuantitas_diminta" value="{{ old('kuantitas_diminta', 1) }}" min="1" required>
                            @error('kuantitas_diminta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pengambilan" class="form-label">Tanggal Pengambilan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_pengambilan') is-invalid @enderror" id="tanggal_pengambilan" name="tanggal_pengambilan" value="{{ old('tanggal_pengambilan', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" required>
                            @error('tanggal_pengambilan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($tipe === 'peminjaman')
                        <div class="mb-3">
                            <label for="jatuh_tempo_pengembalian" class="form-label">Jatuh Tempo Pengembalian <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('jatuh_tempo_pengembalian') is-invalid @enderror" id="jatuh_tempo_pengembalian" name="jatuh_tempo_pengembalian" value="{{ old('jatuh_tempo_pengembalian') }}" min="{{ now()->format('Y-m-d') }}" required>
                            <small class="text-muted">Wajib untuk aset yang dipinjam.</small>
                            @error('jatuh_tempo_pengembalian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="keperluan" class="form-label">Keperluan / Alasan Pengajuan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('keperluan') is-invalid @enderror" id="keperluan" name="keperluan" rows="4" required>{{ old('keperluan') }}</textarea>
                            @error('keperluan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
