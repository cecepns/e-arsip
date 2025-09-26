<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    // Tampilkan daftar surat keluar
    public function index(Request $request)
    {
        // Nanti: filter, hak akses, pencarian
        $bagian = Bagian::where('status', 'Aktif')->get();
        $suratKeluar = SuratKeluar::with('pengirimBagian', 'user')->get();
        return view('pages.surat_keluar.index', compact('suratKeluar', 'bagian'));
    }

    // Tampilkan form tambah surat keluar
    public function create()
    {
        $bagian = Bagian::where('status', 'Aktif')->get();
        return view('pages.surat_keluar.create', compact('bagian'));
    }

    // Simpan surat keluar baru
    public function store(Request $request)
    {
            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:100',
                'tanggal_surat' => 'required|date',
                'tanggal_keluar' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'tujuan' => 'required|string|max:150',
                'keterangan' => 'nullable|string',
                'pengirim_bagian_id' => 'required|exists:bagian,id',
                'lampiran_pdf' => 'required|file|mimes:pdf|max:20480',
                'lampiran_pendukung.*' => 'nullable|file|mimes:zip,rar,docx,xlsx|max:20480',
            ]);
            $validated['user_id'] = Auth::id();
            $suratKeluar = SuratKeluar::create($validated);

            // Proses upload lampiran utama (PDF)
            if ($request->hasFile('lampiran_pdf')) {
                $file = $request->file('lampiran_pdf');
                $path = $file->store('lampiran/surat_keluar', 'public');
                $suratKeluar->lampiran()->create([
                    'tipe_surat' => 'keluar',
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe_lampiran' => 'utama',
                ]);
            }

            // Proses upload dokumen pendukung (multi-file)
            if ($request->hasFile('lampiran_pendukung')) {
                foreach ($request->file('lampiran_pendukung') as $file) {
                    $path = $file->store('lampiran/surat_keluar', 'public');
                    $suratKeluar->lampiran()->create([
                        'tipe_surat' => 'keluar',
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_lampiran' => 'pendukung',
                    ]);
                }
            }

            return redirect()->route('surat_keluar.index')->with('success', 'Surat keluar berhasil ditambahkan.');
    }

    // Tampilkan detail surat keluar
    public function show($id)
    {
        $suratKeluar = SuratKeluar::with('pengirimBagian', 'user')->findOrFail($id);
        return view('pages.surat_keluar.show', compact('suratKeluar'));
    }

    // Tampilkan form edit surat keluar
    public function edit($id)
    {
        $suratKeluar = SuratKeluar::findOrFail($id);
        $bagian = Bagian::all();
        return view('pages.surat_keluar.edit', compact('suratKeluar', 'bagian'));
    }

    // Update surat keluar
    public function update(Request $request, $id)
    {
            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:100',
                'tanggal_surat' => 'required|date',
                'tanggal_keluar' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'tujuan' => 'required|string|max:150',
                'keterangan' => 'nullable|string',
                'pengirim_bagian_id' => 'required|exists:bagian,id',
                'lampiran_pdf' => 'nullable|file|mimes:pdf|max:20480',
                'lampiran_pendukung.*' => 'nullable|file|mimes:zip,rar,docx,xlsx|max:20480',
            ]);
            $suratKeluar = SuratKeluar::findOrFail($id);
            $suratKeluar->update($validated);

            // Update lampiran utama jika ada file baru
            if ($request->hasFile('lampiran_pdf')) {
                $lama = $suratKeluar->lampiran()->where('tipe_lampiran', 'utama')->first();
                if ($lama) {
                    Storage::disk('public')->delete($lama->path_file);
                    $lama->delete();
                }
                $file = $request->file('lampiran_pdf');
                $path = $file->store('lampiran/surat_keluar', 'public');
                $suratKeluar->lampiran()->create([
                    'tipe_surat' => 'keluar',
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe_lampiran' => 'utama',
                ]);
            }

            // Tambah dokumen pendukung baru
            if ($request->hasFile('lampiran_pendukung')) {
                foreach ($request->file('lampiran_pendukung') as $file) {
                    $path = $file->store('lampiran/surat_keluar', 'public');
                    $suratKeluar->lampiran()->create([
                        'tipe_surat' => 'keluar',
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_lampiran' => 'pendukung',
                    ]);
                }
            }

            return redirect()->route('surat_keluar.index')->with('success', 'Surat keluar berhasil diupdate.');
    }

    // Hapus surat keluar
    public function destroy($id)
    {
            $suratKeluar = SuratKeluar::findOrFail($id);
            // Hapus semua lampiran dari storage
            foreach ($suratKeluar->lampiran as $lampiran) {
                Storage::disk('public')->delete($lampiran->path_file);
                $lampiran->delete();
            }
            $suratKeluar->delete();
            return redirect()->route('surat_keluar.index')->with('success', 'Surat keluar berhasil dihapus.');
    }
}
