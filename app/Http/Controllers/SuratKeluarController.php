<?php

namespace App\Http\Controllers;

use App\Models\SuratKeluar;
use App\Models\Bagian;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\AjaxErrorHandler;

class SuratKeluarController extends Controller
{
    use AjaxErrorHandler;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display a listing of the surat keluar.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        $sifatSurat = $request->get('sifat_surat');
        $bagianId = $request->get('bagian_id');
        $tanggal = $request->get('tanggal');
        
        $suratKeluar = SuratKeluar::with(['pengirimBagian', 'user', 'creator', 'updater'])
            ->when(Auth::user() && Auth::user()->role === 'Staf', function ($q) {
                // ANCHOR: Staff hanya bisa melihat surat keluar dari bagiannya
                $q->where('pengirim_bagian_id', Auth::user()->bagian_id);
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQ) use ($query) {
                    $subQ->where('nomor_surat', 'like', "%{$query}%")
                         ->orWhere('perihal', 'like', "%{$query}%")
                         ->orWhere('tujuan', 'like', "%{$query}%");
                });
            })
            ->when($sifatSurat, function ($q) use ($sifatSurat) {
                $q->where('sifat_surat', $sifatSurat);
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('pengirim_bagian_id', $bagianId);
            })
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereDate('tanggal_surat', $tanggal);
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

        return view('pages.surat_keluar.index', compact('suratKeluar', 'bagian', 'filters'));
    }

    /**
     * Store a newly created surat keluar in storage.
     */
    public function store(Request $request)
    {
        try {
            // ANCHOR: Set pengirim_bagian_id untuk Staff otomatis
            $user = Auth::user();
            if ($user && $user->role === 'Staf') {
                $request->merge(['pengirim_bagian_id' => $user->bagian_id]);
            }

            $validated = $request->validate([
                'nomor_surat' => [
                    'required',
                    'string',
                    'max:100',
                    function ($attribute, $value, $fail) {
                        if (SuratKeluar::where('nomor_surat', $value)->exists()) {
                            $fail('Nomor surat sudah digunakan.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'tanggal_keluar' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'tujuan' => 'required|string|max:150',
                'sifat_surat' => 'required|string|in:Biasa,Segera,Penting,Rahasia',
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
                'sifat_surat.required' => 'Sifat surat wajib dipilih.',
                'sifat_surat.string' => 'Sifat surat harus berupa teks.',
                'sifat_surat.in' => 'Sifat surat harus Biasa, Segera, Penting, atau Rahasia.',
                'pengirim_bagian_id.required' => 'Bagian pengirim wajib dipilih.',
                'pengirim_bagian_id.exists' => 'Bagian pengirim yang dipilih tidak valid.',
                'lampiran_pdf.required' => 'Lampiran PDF wajib diupload.',
                'lampiran_pdf.file' => 'Lampiran PDF harus berupa file.',
                'lampiran_pdf.mimes' => 'Lampiran PDF harus berupa file PDF.',
                'lampiran_pdf.max' => 'Lampiran PDF maksimal 20MB.',
            ]);

            $validated['user_id'] = Auth::id();
            // ANCHOR: Audit fields (created_by, updated_by) are automatically handled by Auditable trait
            $suratKeluar = SuratKeluar::create($validated);

            // ANCHOR: Send notification for new surat keluar
            $this->notificationService->sendSuratKeluarNotification($suratKeluar);

            // ANCHOR: Process PDF attachment upload
            if ($request->hasFile('lampiran_pdf')) {
                $file = $request->file('lampiran_pdf');
                $path = $file->store('lampiran/surat_keluar', 'public');
                $suratKeluar->lampiran()->create([
                    'tipe_surat' => 'keluar',
                    'nama_file' => $file->getClientOriginalName(),
                    'path_file' => $path,
                    'file_size' => $file->getSize(),
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
                        'file_size' => $file->getSize(),
                        'tipe_lampiran' => 'pendukung',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Surat keluar berhasil ditambahkan.',
                'suratKeluar' => $suratKeluar->load('pengirimBagian', 'user'),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * Display the specified surat keluar.
     */
    public function show(Request $request, string $id)
    {
        try {
            $suratKeluar = SuratKeluar::with(['pengirimBagian', 'user', 'lampiran', 'creator', 'updater'])->findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'Staf' && $suratKeluar->pengirim_bagian_id !== $user->bagian_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk melihat surat ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk melihat surat ini.');
            }
            
            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'suratKeluar' => $suratKeluar,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }
            
            // For non-AJAX requests, you could return a dedicated view
            return response()->json([
                'success' => true,
                'suratKeluar' => $suratKeluar
            ]);
            
        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * Update the specified surat keluar in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $suratKeluar = SuratKeluar::findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'Staf' && $suratKeluar->pengirim_bagian_id !== $user->bagian_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk mengedit surat ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk mengedit surat ini.');
            }

            // ANCHOR: Set pengirim_bagian_id untuk Staff otomatis
            if ($user && $user->role === 'Staf') {
                $request->merge(['pengirim_bagian_id' => $user->bagian_id]);
            }

            $validated = $request->validate([
                'nomor_surat' => [
                    'required',
                    'string',
                    'max:100',
                    function ($attribute, $value, $fail) use ($id) {
                        $query = SuratKeluar::where('nomor_surat', $value);
                        if ($id) {
                            $query->where('id', '!=', $id);
                        }
                        if ($query->exists()) {
                            $fail('Nomor surat sudah digunakan.');
                        }
                    }
                ],
                'tanggal_surat' => 'required|date',
                'tanggal_keluar' => 'required|date',
                'perihal' => 'required|string|max:255',
                'ringkasan_isi' => 'nullable|string',
                'tujuan' => 'required|string|max:150',
                'sifat_surat' => 'required|string|in:Biasa,Segera,Penting,Rahasia',
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
                'sifat_surat.required' => 'Sifat surat wajib dipilih.',
                'sifat_surat.string' => 'Sifat surat harus berupa teks.',
                'sifat_surat.in' => 'Sifat surat harus Biasa, Segera, Penting, atau Rahasia.',
                'pengirim_bagian_id.required' => 'Bagian pengirim wajib dipilih.',
                'pengirim_bagian_id.exists' => 'Bagian pengirim yang dipilih tidak valid.',
                'lampiran_pdf.file' => 'Lampiran PDF harus berupa file.',
                'lampiran_pdf.mimes' => 'Lampiran PDF harus berupa file PDF.',
                'lampiran_pdf.max' => 'Lampiran PDF maksimal 20MB.',
            ]);

            // ANCHOR: Audit fields (updated_by) are automatically handled by Auditable trait
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
                    'file_size' => $file->getSize(),
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
                        'file_size' => $file->getSize(),
                        'tipe_lampiran' => 'pendukung',
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Surat keluar '{$suratKeluar->nomor_surat}' berhasil diperbarui.",
                'suratKeluar' => $suratKeluar->load('pengirimBagian', 'user'),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
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
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'Staf' && $suratKeluar->pengirim_bagian_id !== $user->bagian_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk menghapus surat ini.'
                    ], 403);
                }
                abort(403, 'Anda tidak memiliki akses untuk menghapus surat ini.');
            }
            
            $suratKeluar->delete();

            return response()->json([
                'success' => true,
                'message' => "Surat keluar '{$nomorSurat}' berhasil dihapus.",
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }
}
