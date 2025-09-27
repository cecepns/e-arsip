<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratKeluarController extends Controller
{
    /**
     * Display a listing of the surat keluar.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        
        $suratKeluar = SuratKeluar::with('pengirimBagian', 'user')
            ->when($query, function ($q) use ($query) {
                $q->where('nomor_surat', 'like', "%{$query}%")
                  ->orWhere('perihal', 'like', "%{$query}%")
                  ->orWhere('tujuan', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $bagian = Bagian::where('status', 'Aktif')->get();

        return view('pages.surat_keluar.index', compact('suratKeluar', 'query', 'bagian'));
    }

    /**
     * Store a newly created surat keluar in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:100|unique:surat_keluar,nomor_surat',
                'tanggal_surat' => 'required|date',
                'tanggal_keluar' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'tujuan' => 'required|string|max:150',
                'keterangan' => 'nullable|string',
                'pengirim_bagian_id' => 'required|exists:bagian,id',
                'lampiran_pdf' => 'required|file|mimes:pdf|max:20480',
                'lampiran_pendukung.*' => 'nullable|file|mimes:zip,rar,docx,xlsx|max:20480',
            ], [
                'nomor_surat.required' => 'Nomor surat wajib diisi.',
                'nomor_surat.string' => 'Nomor surat harus berupa teks.',
                'nomor_surat.max' => 'Nomor surat maksimal 100 karakter.',
                'nomor_surat.unique' => 'Nomor surat sudah digunakan.',
                'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
                'tanggal_surat.date' => 'Format tanggal surat tidak valid.',
                'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
                'tanggal_keluar.date' => 'Format tanggal keluar tidak valid.',
                'perihal.required' => 'Perihal wajib diisi.',
                'perihal.string' => 'Perihal harus berupa teks.',
                'perihal.max' => 'Perihal maksimal 255 karakter.',
                'tujuan.required' => 'Tujuan wajib diisi.',
                'tujuan.string' => 'Tujuan harus berupa teks.',
                'tujuan.max' => 'Tujuan maksimal 150 karakter.',
                'pengirim_bagian_id.required' => 'Bagian pengirim wajib dipilih.',
                'pengirim_bagian_id.exists' => 'Bagian pengirim yang dipilih tidak valid.',
                'lampiran_pdf.required' => 'Lampiran PDF wajib diupload.',
                'lampiran_pdf.file' => 'Lampiran PDF harus berupa file.',
                'lampiran_pdf.mimes' => 'Lampiran PDF harus berupa file PDF.',
                'lampiran_pdf.max' => 'Lampiran PDF maksimal 20MB.',
            ]);

            $validated['user_id'] = Auth::id();
            $suratKeluar = SuratKeluar::create($validated);

            // ANCHOR: Process PDF attachment upload
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

            // ANCHOR: Process supporting documents upload
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

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat keluar berhasil ditambahkan.',
                    'suratKeluar' => $suratKeluar->load('pengirimBagian', 'user'),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 201);
            }

            return redirect()->route('surat_keluar.index')
                ->with('success', 'Surat keluar berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ANCHOR: Handle validation errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa data yang dimasukkan.',
                    'errors' => $e->errors(),
                    'error_type' => 'validation'
                ], 422);
            }
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // ANCHOR: Handle database errors
            if ($request->ajax()) {
                $errorMessage = 'Terjadi kesalahan database.';
                
                // Check for specific database errors
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $errorMessage = 'Data sudah ada dalam sistem.';
                } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                    $errorMessage = 'Data bagian tidak valid.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_type' => 'database',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            throw $e;

        } catch (\Exception $e) {
            // ANCHOR: Handle general errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                    'error_type' => 'general',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Update the specified surat keluar in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $suratKeluar = SuratKeluar::findOrFail($id);

            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:100|unique:surat_keluar,nomor_surat,' . $id,
                'tanggal_surat' => 'required|date',
                'tanggal_keluar' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'tujuan' => 'required|string|max:150',
                'keterangan' => 'nullable|string',
                'pengirim_bagian_id' => 'required|exists:bagian,id',
                'lampiran_pdf' => 'nullable|file|mimes:pdf|max:20480',
                'lampiran_pendukung.*' => 'nullable|file|mimes:zip,rar,docx,xlsx|max:20480',
            ], [
                'nomor_surat.required' => 'Nomor surat wajib diisi.',
                'nomor_surat.string' => 'Nomor surat harus berupa teks.',
                'nomor_surat.max' => 'Nomor surat maksimal 100 karakter.',
                'nomor_surat.unique' => 'Nomor surat sudah digunakan.',
                'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
                'tanggal_surat.date' => 'Format tanggal surat tidak valid.',
                'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
                'tanggal_keluar.date' => 'Format tanggal keluar tidak valid.',
                'perihal.required' => 'Perihal wajib diisi.',
                'perihal.string' => 'Perihal harus berupa teks.',
                'perihal.max' => 'Perihal maksimal 255 karakter.',
                'tujuan.required' => 'Tujuan wajib diisi.',
                'tujuan.string' => 'Tujuan harus berupa teks.',
                'tujuan.max' => 'Tujuan maksimal 150 karakter.',
                'pengirim_bagian_id.required' => 'Bagian pengirim wajib dipilih.',
                'pengirim_bagian_id.exists' => 'Bagian pengirim yang dipilih tidak valid.',
                'lampiran_pdf.file' => 'Lampiran PDF harus berupa file.',
                'lampiran_pdf.mimes' => 'Lampiran PDF harus berupa file PDF.',
                'lampiran_pdf.max' => 'Lampiran PDF maksimal 20MB.',
            ]);

            $suratKeluar->update($validated);

            // ANCHOR: Update PDF attachment if new file uploaded
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

            // ANCHOR: Add new supporting documents
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

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Surat keluar '{$suratKeluar->nomor_surat}' berhasil diperbarui.",
                    'suratKeluar' => $suratKeluar->load('pengirimBagian', 'user'),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('surat_keluar.index')
                ->with('success', "Surat keluar '{$suratKeluar->nomor_surat}' berhasil diperbarui.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ANCHOR: Handle validation errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal. Periksa data yang dimasukkan.',
                    'errors' => $e->errors(),
                    'error_type' => 'validation'
                ], 422);
            }
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // ANCHOR: Handle database errors
            if ($request->ajax()) {
                $errorMessage = 'Terjadi kesalahan database.';
                
                // Check for specific database errors
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $errorMessage = 'Data sudah ada dalam sistem.';
                } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                    $errorMessage = 'Data bagian tidak valid.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_type' => 'database',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            throw $e;

        } catch (\Exception $e) {
            // ANCHOR: Handle general errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                    'error_type' => 'general',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified surat keluar from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $suratKeluar = SuratKeluar::findOrFail($id);
            $nomorSurat = $suratKeluar->nomor_surat;
            
            $suratKeluar->delete();

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Surat keluar '{$nomorSurat}' berhasil dihapus.",
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('surat_keluar.index')
                ->with('success', "Surat keluar '{$nomorSurat}' berhasil dihapus.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ANCHOR: Handle surat keluar not found
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat keluar tidak ditemukan.',
                    'error_type' => 'not_found'
                ], 404);
            }
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // ANCHOR: Handle database errors
            if ($request->ajax()) {
                $errorMessage = 'Terjadi kesalahan database.';
                
                // Check for specific database errors
                if (str_contains($e->getMessage(), 'foreign key constraint')) {
                    $errorMessage = 'Surat keluar tidak dapat dihapus karena memiliki data terkait.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_type' => 'database',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            throw $e;

        } catch (\Exception $e) {
            // ANCHOR: Handle general errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                    'error_type' => 'general',
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            throw $e;
        }
    }
}
