<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use Illuminate\Http\Request;

class BagianController extends Controller
{
    // Tampilkan halaman manajemen bagian
    public function index(Request $request)
    {
        $query = $request->get('search');
        $bagian = Bagian::where('nama_bagian', 'like', '%' . $query . '%')->get();
        return view('pages.bagian.index', compact('bagian', 'query'));
    }

    // Simpan data bagian baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'kepala_bagian' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
            'keterangan' => 'nullable|string',
        ]);
        Bagian::create($validated);
        return redirect()->route('bagian.index')->with('success', 'Bagian berhasil ditambahkan.');
    }

    // Update data bagian
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'kepala_bagian' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
            'keterangan' => 'nullable|string',
        ]);
        $bagian = Bagian::findOrFail($id);
        $bagian->update($validated);
        return redirect()->route('bagian.index')->with('success', 'Bagian berhasil diupdate.');
    }

    // Hapus data bagian
    public function destroy($id)
    {
        $bagian = Bagian::findOrFail($id);
        $bagian->delete();
        return redirect()->route('bagian.index')->with('success', 'Bagian berhasil dihapus.');
    }

    // Tampilkan detail bagian (opsional, untuk modal detail)
    public function show($id)
    {
        $bagian = Bagian::findOrFail($id);
        return response()->json($bagian);
    }
}
