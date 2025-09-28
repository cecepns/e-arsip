<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use App\Models\Bagian;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratMasukController extends Controller
{
    /**
     * Display a listing of the surat masuk.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        $sifatSurat = $request->get('sifat_surat');
        $bagianId = $request->get('bagian_id');
        $tanggal = $request->get('tanggal');
        
        $suratMasuk = SuratMasuk::with(['tujuanBagian', 'user', 'creator', 'updater', 'disposisi'])
            ->when($query, function ($q) use ($query) {
                $q->where('nomor_surat', 'like', "%{$query}%")
                  ->orWhere('perihal', 'like', "%{$query}%")
                  ->orWhere('pengirim', 'like', "%{$query}%");
            })
            ->when($sifatSurat, function ($q) use ($sifatSurat) {
                $q->where('sifat_surat', $sifatSurat);
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('tujuan_bagian_id', $bagianId);
            })
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereDate('tanggal_surat', $tanggal);
            })
            ->when(auth()->user()->role === 'staf', function ($q) {
                // ANCHOR: Staf hanya bisa melihat surat yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', auth()->user()->bagian_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $bagian = Bagian::where('status', 'Aktif')->get();

        // Collect filter values for form
        $filters = [
            'query' => $query,
            'sifat_surat' => $sifatSurat,
            'bagian_id' => $bagianId,
            'tanggal' => $tanggal,
        ];

        return view('pages.surat_masuk.index', compact('suratMasuk', 'bagian', 'filters'));
    }

    /**
     * Store a newly created surat masuk in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:100|unique:surat_masuk,nomor_surat',
                'tanggal_surat' => 'required|date',
                'tanggal_terima' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'pengirim' => 'required|string|max:150',
                'sifat_surat' => 'required|string|in:Biasa,Segera,Penting,Rahasia',
                'keterangan' => 'nullable|string',
                'tujuan_bagian_id' => 'required|exists:bagian,id',
                'lampiran_pdf' => 'required|file|mimes:pdf|max:20480',
                'lampiran_pendukung.*' => 'nullable|file|mimes:zip,rar,docx,xlsx|max:20480',
                'buat_disposisi' => 'nullable|boolean',
                'disposisi_tujuan_bagian_id' => 'required_if:buat_disposisi,1|exists:bagian,id',
                'disposisi_prioritas' => 'required_if:buat_disposisi,1|string|in:Normal,Tinggi,Sangat Tinggi',
                'disposisi_instruksi' => 'required_if:buat_disposisi,1|string',
                'disposisi_catatan' => 'nullable|string',
            ]);

            $validated['user_id'] = Auth::id();
            // ANCHOR: Audit fields (created_by, updated_by) are automatically handled by Auditable trait
            $suratMasuk = SuratMasuk::create($validated);

            // ANCHOR: Create disposisi if requested
            if ($request->has('buat_disposisi') && $request->buat_disposisi) {
                $disposisi = Disposisi::create([
                    'surat_masuk_id' => $suratMasuk->id,
                    'tujuan_bagian_id' => $validated['disposisi_tujuan_bagian_id'],
                    'prioritas' => $validated['disposisi_prioritas'],
                    'instruksi' => $validated['disposisi_instruksi'],
                    'catatan' => $validated['disposisi_catatan'] ?? null,
                    'status' => 'Belum Dikerjakan',
                    'user_id' => Auth::id(),
                ]);
            }

            // ANCHOR: Process PDF attachment upload
            if ($request->hasFile('lampiran_pdf')) {
                $file = $request->file('lampiran_pdf');
                $path = $file->store('lampiran/surat_masuk', 'public');
                $suratMasuk->lampiran()->create([
                    'tipe_surat' => 'masuk',
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe_lampiran' => 'utama',
                ]);
            }

            // ANCHOR: Process supporting documents upload
            if ($request->hasFile('lampiran_pendukung')) {
                foreach ($request->file('lampiran_pendukung') as $file) {
                    $path = $file->store('lampiran/surat_masuk', 'public');
                    $suratMasuk->lampiran()->create([
                        'tipe_surat' => 'masuk',
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
                    'message' => 'Surat masuk berhasil ditambahkan.',
                    'suratMasuk' => $suratMasuk->load(['tujuanBagian', 'user', 'creator', 'updater', 'disposisi']),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 201);
            }

            return redirect()->route('surat_masuk.index')
                ->with('success', 'Surat masuk berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // ANCHOR: Handle validation errors
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $e->errors(),
                    'error_type' => 'validation'
                ], 422);
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
     * Display the specified surat masuk.
     */
    public function show(Request $request, string $id)
    {
        try {
            $suratMasuk = SuratMasuk::with(['tujuanBagian', 'user', 'lampiran', 'creator', 'updater', 'disposisi.tujuanBagian'])->findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            if (auth()->user()->role === 'staf' && $suratMasuk->tujuan_bagian_id !== auth()->user()->bagian_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk melihat surat ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk melihat surat ini.');
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'suratMasuk' => $suratMasuk,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }
            
            return response()->json([
                'success' => true,
                'suratMasuk' => $suratMasuk
            ]);
            
        } catch (\Exception $e) {
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
     * Update the specified surat masuk in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $suratMasuk = SuratMasuk::findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            if (auth()->user()->role === 'staf' && $suratMasuk->tujuan_bagian_id !== auth()->user()->bagian_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk mengedit surat ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk mengedit surat ini.');
            }

            $validated = $request->validate([
                'nomor_surat' => 'required|string|max:100|unique:surat_masuk,nomor_surat,' . $id,
                'tanggal_surat' => 'required|date',
                'tanggal_terima' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'pengirim' => 'required|string|max:150',
                'sifat_surat' => 'required|string|in:Biasa,Segera,Penting,Rahasia',
                'keterangan' => 'nullable|string',
                'tujuan_bagian_id' => 'required|exists:bagian,id',
                'lampiran_pdf' => 'nullable|file|mimes:pdf|max:20480',
                'lampiran_pendukung.*' => 'nullable|file|mimes:zip,rar,docx,xlsx|max:20480',
            ]);

            // ANCHOR: Audit fields (updated_by) are automatically handled by Auditable trait
            $suratMasuk->update($validated);

            // ANCHOR: Update PDF attachment if new file uploaded
            if ($request->hasFile('lampiran_pdf')) {
                $lama = $suratMasuk->lampiran()->where('tipe_lampiran', 'utama')->first();
                if ($lama) {
                    Storage::disk('public')->delete($lama->path_file);
                    $lama->delete();
                }
                $file = $request->file('lampiran_pdf');
                $path = $file->store('lampiran/surat_masuk', 'public');
                $suratMasuk->lampiran()->create([
                    'tipe_surat' => 'masuk',
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'tipe_lampiran' => 'utama',
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat masuk berhasil diperbarui.',
                    'suratMasuk' => $suratMasuk->load(['tujuanBagian', 'user', 'creator', 'updater', 'disposisi']),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('surat_masuk.index')
                ->with('success', 'Surat masuk berhasil diperbarui.');

        } catch (\Exception $e) {
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
     * Remove the specified surat masuk from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $suratMasuk = SuratMasuk::findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            if (auth()->user()->role === 'staf' && $suratMasuk->tujuan_bagian_id !== auth()->user()->bagian_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk menghapus surat ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk menghapus surat ini.');
            }

            // ANCHOR: Delete associated lampiran files
            foreach ($suratMasuk->lampiran as $lampiran) {
                Storage::disk('public')->delete($lampiran->path_file);
            }

            $suratMasuk->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Surat masuk berhasil dihapus.',
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('surat_masuk.index')
                ->with('success', 'Surat masuk berhasil dihapus.');

        } catch (\Exception $e) {
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
