<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        // Permission check (jika pakai Spatie)
        if (!Auth::user()->hasPermissionTo('view-vendor')) {
            abort(403);
        }

        $search = $request->input('search');
        $query = Vendor::orderBy('nama_vendor', 'asc');

        if ($search) {
            $query->where('nama_vendor', 'like', "%{$search}%")
                  ->orWhere('kontak_email', 'like', "%{$search}%");
        }

        $vendors = $query->paginate(15);

        return view('vendors.index', compact('vendors', 'search'));
    }

    public function create()
    {
        if (!Auth::user()->hasPermissionTo('create-vendor')) {
            abort(403);
        }
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('create-vendor')) {
            abort(403);
        }

        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak_email' => 'nullable|email',
            'kontak_telepon' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        Vendor::create($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function show(Vendor $vendor)
    {
        if (!Auth::user()->hasPermissionTo('view-vendor')) {
            abort(403);
        }
        return view('vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        if (!Auth::user()->hasPermissionTo('edit-vendor')) {
            abort(403);
        }
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        if (!Auth::user()->hasPermissionTo('edit-vendor')) {
            abort(403);
        }

        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kontak_email' => 'nullable|email',
            'kontak_telepon' => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        if (!Auth::user()->hasPermissionTo('delete-vendor')) {
            abort(403);
        }

        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor berhasil dihapus.');
    }
}