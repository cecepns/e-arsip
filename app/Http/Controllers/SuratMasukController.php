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
use App\Traits\AjaxErrorHandler;

class SuratMasukController extends Controller
{
    use AjaxErrorHandler;
    /**
     * Display a listing of the surat masuk.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        $sifatSurat = $request->get('sifat_surat');
        $bagianId = $request->get('bagian_id');
        $tanggal = $request->get('tanggal');
        
        $suratMasuk = SuratMasuk::with(['tujuanBagian', 'user', 'creator', 'updater', 'disposisi.tujuanBagian'])
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
            ->when(Auth::user() && Auth::user()->role === 'staf', function ($q) {
                // ANCHOR: Staf hanya bisa melihat surat yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', Auth::user()->bagian_id);
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
                'disposisi' => 'nullable|array',
                'disposisi.*.tujuan_bagian_id' => 'required_with:disposisi|exists:bagian,id|different:tujuan_bagian_id',
                'disposisi.*.status' => 'required_with:disposisi|string|in:Menunggu,Dikerjakan,Selesai',
                'disposisi.*.instruksi' => 'required_with:disposisi|string',
                'disposisi.*.catatan' => 'nullable|string',
                'disposisi.*.tanggal_disposisi' => 'nullable|date',
                'disposisi.*.batas_waktu' => 'nullable|date|after_or_equal:disposisi.*.tanggal_disposisi',
            ], [
                'nomor_surat.required' => 'Nomor surat wajib diisi.',
                'nomor_surat.string' => 'Nomor surat harus berupa teks.',
                'nomor_surat.max' => 'Nomor surat maksimal 100 karakter.',
                'nomor_surat.unique' => 'Nomor surat sudah digunakan.',
                'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
                'tanggal_surat.date' => 'Format tanggal surat tidak valid.',
                'tanggal_terima.required' => 'Tanggal terima wajib diisi.',
                'tanggal_terima.date' => 'Format tanggal terima tidak valid.',
                'perihal.required' => 'Perihal wajib diisi.',
                'perihal.string' => 'Perihal harus berupa teks.',
                'perihal.max' => 'Perihal maksimal 255 karakter.',
                'pengirim.required' => 'Pengirim wajib diisi.',
                'pengirim.string' => 'Pengirim harus berupa teks.',
                'pengirim.max' => 'Pengirim maksimal 150 karakter.',
                'sifat_surat.required' => 'Sifat surat wajib dipilih.',
                'sifat_surat.string' => 'Sifat surat harus berupa teks.',
                'sifat_surat.in' => 'Sifat surat harus Biasa, Segera, Penting, atau Rahasia.',
                'tujuan_bagian_id.required' => 'Bagian tujuan wajib dipilih.',
                'tujuan_bagian_id.exists' => 'Bagian tujuan yang dipilih tidak valid.',
                'lampiran_pdf.required' => 'Lampiran PDF wajib diupload.',
                'lampiran_pdf.file' => 'Lampiran PDF harus berupa file.',
                'lampiran_pdf.mimes' => 'Lampiran PDF harus berupa file PDF.',
                'lampiran_pdf.max' => 'Lampiran PDF maksimal 20MB.',
                'lampiran_pendukung.*.file' => 'Dokumen pendukung harus berupa file.',
                'lampiran_pendukung.*.mimes' => 'Dokumen pendukung harus berupa ZIP, RAR, DOCX, atau XLSX.',
                'lampiran_pendukung.*.max' => 'Dokumen pendukung maksimal 20MB per file.',
                'disposisi.required_if' => 'Minimal satu disposisi harus ditambahkan jika membuat disposisi.',
                'disposisi.array' => 'Data disposisi harus berupa array.',
                'disposisi.*.tujuan_bagian_id.required_with' => 'Tujuan disposisi wajib dipilih.',
                'disposisi.*.tujuan_bagian_id.exists' => 'Tujuan disposisi yang dipilih tidak valid.',
                'disposisi.*.tujuan_bagian_id.different' => 'Tujuan disposisi harus berbeda dengan bagian tujuan surat.',
                'disposisi.*.status.required_with' => 'Status disposisi wajib dipilih.',
                'disposisi.*.status.in' => 'Status harus Menunggu, Dikerjakan, atau Selesai.',
                'disposisi.*.instruksi.required_with' => 'Instruksi disposisi wajib diisi.',
                'disposisi.*.instruksi.string' => 'Instruksi harus berupa teks.',
                'disposisi.*.catatan.string' => 'Catatan harus berupa teks.',
                'disposisi.*.tanggal_disposisi.date' => 'Format tanggal disposisi tidak valid.',
                'disposisi.*.batas_waktu.date' => 'Format batas waktu tidak valid.',
                'disposisi.*.batas_waktu.after_or_equal' => 'Batas waktu harus sama atau setelah tanggal disposisi.',
            ]);

            $validated['user_id'] = Auth::id();
            // ANCHOR: Audit fields (created_by, updated_by) are automatically handled by Auditable trait
            $suratMasuk = SuratMasuk::create($validated);

            // ANCHOR: Create multiple disposisi if provided
            if ($request->has('disposisi') && 
                is_array($request->disposisi) && 
                !empty($request->disposisi)) {
                
                foreach ($request->disposisi as $disposisiData) {
                    // ANCHOR: Check if disposisi already exists for this surat and bagian
                    $existingDisposisi = Disposisi::where('surat_masuk_id', $suratMasuk->id)
                        ->where('tujuan_bagian_id', $disposisiData['tujuan_bagian_id'])
                        ->first();
                    
                    if (!$existingDisposisi) {
                        Disposisi::create([
                            'surat_masuk_id' => $suratMasuk->id,
                            'tujuan_bagian_id' => $disposisiData['tujuan_bagian_id'],
                            'isi_instruksi' => $disposisiData['instruksi'],
                            'catatan' => $disposisiData['catatan'] ?? null,
                            'status' => $disposisiData['status'] ?? 'Menunggu',
                            'tanggal_disposisi' => $disposisiData['tanggal_disposisi'] ?? null,
                            'batas_waktu' => $disposisiData['batas_waktu'] ?? null,
                            'user_id' => Auth::id(),
                        ]);
                    }
                }
            }

            // ANCHOR: Process PDF attachment upload
            if ($request->hasFile('lampiran_pdf')) {
                $file = $request->file('lampiran_pdf');
                $path = $file->store('lampiran/surat_masuk', 'public');
                $suratMasuk->lampiran()->create([
                    'tipe_surat' => 'masuk',
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'file_size' => $file->getSize(),
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
                        'file_size' => $file->getSize(),
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
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * Display the specified surat masuk.
     */
    public function show(Request $request, string $id)
    {
        try {
            $suratMasuk = SuratMasuk::with([
                'tujuanBagian.kepalaBagian', 
                'user', 
                'lampiran', 
                'creator', 
                'updater', 
                'disposisi.tujuanBagian.kepalaBagian',
                'disposisi.user.bagian.kepalaBagian'
            ])->findOrFail($id);

            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'staf' && $suratMasuk->tujuan_bagian_id !== $user->bagian_id) {
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
            return $this->handleAjaxError($request, $e);
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
            $user = Auth::user();
            if ($user && $user->role === 'staf' && $suratMasuk->tujuan_bagian_id !== $user->bagian_id) {
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
                'disposisi' => 'nullable|array',
                'disposisi.*.tujuan_bagian_id' => 'required_with:disposisi|exists:bagian,id|different:tujuan_bagian_id',
                'disposisi.*.status' => 'required_with:disposisi|string|in:Menunggu,Dikerjakan,Selesai',
                'disposisi.*.instruksi' => 'required_with:disposisi|string',
                'disposisi.*.catatan' => 'nullable|string',
                'disposisi.*.tanggal_disposisi' => 'nullable|date',
                'disposisi.*.batas_waktu' => 'nullable|date|after_or_equal:disposisi.*.tanggal_disposisi',
            ], [
                'nomor_surat.required' => 'Nomor surat wajib diisi.',
                'nomor_surat.string' => 'Nomor surat harus berupa teks.',
                'nomor_surat.max' => 'Nomor surat maksimal 100 karakter.',
                'nomor_surat.unique' => 'Nomor surat sudah digunakan.',
                'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
                'tanggal_surat.date' => 'Format tanggal surat tidak valid.',
                'tanggal_terima.required' => 'Tanggal terima wajib diisi.',
                'tanggal_terima.date' => 'Format tanggal terima tidak valid.',
                'perihal.required' => 'Perihal wajib diisi.',
                'perihal.string' => 'Perihal harus berupa teks.',
                'perihal.max' => 'Perihal maksimal 255 karakter.',
                'pengirim.required' => 'Pengirim wajib diisi.',
                'pengirim.string' => 'Pengirim harus berupa teks.',
                'pengirim.max' => 'Pengirim maksimal 150 karakter.',
                'sifat_surat.required' => 'Sifat surat wajib dipilih.',
                'sifat_surat.string' => 'Sifat surat harus berupa teks.',
                'sifat_surat.in' => 'Sifat surat harus Biasa, Segera, Penting, atau Rahasia.',
                'tujuan_bagian_id.required' => 'Bagian tujuan wajib dipilih.',
                'tujuan_bagian_id.exists' => 'Bagian tujuan yang dipilih tidak valid.',
                'lampiran_pdf.file' => 'Lampiran PDF harus berupa file.',
                'lampiran_pdf.mimes' => 'Lampiran PDF harus berupa file PDF.',
                'lampiran_pdf.max' => 'Lampiran PDF maksimal 20MB.',
                'lampiran_pendukung.*.file' => 'Dokumen pendukung harus berupa file.',
                'lampiran_pendukung.*.mimes' => 'Dokumen pendukung harus berupa ZIP, RAR, DOCX, atau XLSX.',
                'lampiran_pendukung.*.max' => 'Dokumen pendukung maksimal 20MB per file.',
                'disposisi.array' => 'Data disposisi harus berupa array.',
                'disposisi.*.tujuan_bagian_id.required_with' => 'Tujuan disposisi wajib dipilih.',
                'disposisi.*.tujuan_bagian_id.exists' => 'Tujuan disposisi yang dipilih tidak valid.',
                'disposisi.*.tujuan_bagian_id.different' => 'Tujuan disposisi harus berbeda dengan bagian tujuan surat.',
                'disposisi.*.status.required_with' => 'Status disposisi wajib dipilih.',
                'disposisi.*.status.in' => 'Status harus Menunggu, Dikerjakan, atau Selesai.',
                'disposisi.*.instruksi.required_with' => 'Instruksi disposisi wajib diisi.',
                'disposisi.*.instruksi.string' => 'Instruksi harus berupa teks.',
                'disposisi.*.catatan.string' => 'Catatan harus berupa teks.',
                'disposisi.*.tanggal_disposisi.date' => 'Format tanggal disposisi tidak valid.',
                'disposisi.*.batas_waktu.date' => 'Format batas waktu tidak valid.',
                'disposisi.*.batas_waktu.after_or_equal' => 'Batas waktu harus sama atau setelah tanggal disposisi.',
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
                    'file_size' => $file->getSize(),
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
                        'file_size' => $file->getSize(),
                        'tipe_lampiran' => 'pendukung',
                    ]);
                }
            }

            // ANCHOR: Handle disposisi updates
            if ($request->has('disposisi')) {
                // ANCHOR: Get current disposisi IDs from request
                $requestDisposisiIds = [];
                if (is_array($request->disposisi) && !empty($request->disposisi)) {
                    foreach ($request->disposisi as $disposisiData) {
                        $requestDisposisiIds[] = $disposisiData['tujuan_bagian_id'];
                        
                        // ANCHOR: Update or create disposisi
                        Disposisi::updateOrCreate(
                            [
                                'surat_masuk_id' => $suratMasuk->id,
                                'tujuan_bagian_id' => $disposisiData['tujuan_bagian_id']
                            ],
                            [
                                'isi_instruksi' => $disposisiData['instruksi'],
                                'catatan' => $disposisiData['catatan'] ?? null,
                                'status' => $disposisiData['status'] ?? 'Menunggu',
                                'tanggal_disposisi' => $disposisiData['tanggal_disposisi'] ?? null,
                                'batas_waktu' => $disposisiData['batas_waktu'] ?? null,
                                'user_id' => Auth::id(),
                            ]
                        );
                    }
                }
                
                // ANCHOR: Remove disposisi that are no longer in the request
                Disposisi::where('surat_masuk_id', $suratMasuk->id)
                    ->whereNotIn('tujuan_bagian_id', $requestDisposisiIds)
                    ->delete();
            } else {
                // ANCHOR: If no disposisi in request, remove all existing disposisi
                Disposisi::where('surat_masuk_id', $suratMasuk->id)->delete();
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
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * Remove the specified surat masuk from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $suratMasuk = SuratMasuk::findOrFail($id);
            $nomorSurat = $suratMasuk->nomor_surat;
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'staf' && $suratMasuk->tujuan_bagian_id !== $user->bagian_id) {
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
                    'message' => "Surat masuk '{$nomorSurat}' berhasil dihapus.",
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('surat_masuk.index')
                ->with('success', "Surat masuk '{$nomorSurat}' berhasil dihapus.");

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }
}
