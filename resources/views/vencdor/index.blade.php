@extends('layouts.app')

@section('title', 'Daftar Vendor')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="fw-bold mb-4">Daftar Vendor</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <form action="{{ route('vendors.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari vendor..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
            @if (Auth::user()->hasPermissionTo('create-vendor'))
                <a href="{{ route('vendors.create') }}" class="btn btn-success">Tambah Vendor</a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Vendor</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->nama_vendor }}</td>
                            <td>{{ $vendor->kontak_email ?? '-' }}</td>
                            <td>{{ $vendor->kontak_telepon ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $vendor->status == 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($vendor->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-info btn-sm">Detail</a>
                                @if (Auth::user()->hasPermissionTo('edit-vendor'))
                                    <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endif
                                @if (Auth::user()->hasPermissionTo('delete-vendor'))
                                    <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $vendors->links() }}
        </div>
    </div>
</div>
@endsection