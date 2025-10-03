<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Traits\AjaxErrorHandler;

class DisposisiController extends Controller
{
    use AjaxErrorHandler;

    /**
     * Display a listing of the disposisi.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        $status = $request->get('status');
        $bagianId = $request->get('bagian_id');
        $tanggal = $request->get('tanggal');
        $sifatSurat = $request->get('sifat_surat');
        
        $disposisi = Disposisi::with([
            'suratMasuk.tujuanBagian.kepalaBagian', 
            'tujuanBagian.kepalaBagian', 
            'user', 
            'creator', 
            'updater'
        ])
            ->when(Auth::user() && Auth::user()->role === 'Staf', function ($q) {
                // ANCHOR: Staf bisa melihat disposisi "ke bagiannya" dan "dari bagiannya"
                $q->where(function ($subQ) {
                    $subQ->where('tujuan_bagian_id', Auth::user()->bagian_id) // Disposisi KE bagiannya
                         ->orWhereHas('suratMasuk', function ($suratQ) {
                             $suratQ->where('tujuan_bagian_id', Auth::user()->bagian_id); // Disposisi DARI bagiannya
                         });
                });
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQ) use ($query) {
                    $subQ->whereHas('suratMasuk', function ($suratQ) use ($query) {
                        $suratQ->where('nomor_surat', 'like', "%{$query}%")
                               ->orWhere('perihal', 'like', "%{$query}%")
                               ->orWhere('pengirim', 'like', "%{$query}%");
                    })
                    ->orWhere('isi_instruksi', 'like', "%{$query}%")
                    ->orWhere('catatan', 'like', "%{$query}%");
                });
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($bagianId, function ($q) use ($bagianId) {
                $q->where('tujuan_bagian_id', $bagianId);
            })
            ->when($tanggal, function ($q) use ($tanggal) {
                $q->whereDate('tanggal_disposisi', $tanggal);
            })
            ->when($sifatSurat, function ($q) use ($sifatSurat) {
                $q->whereHas('suratMasuk', function ($subQ) use ($sifatSurat) {
                    $subQ->where('sifat_surat', $sifatSurat);
                });
            })
            ->when(Auth::user() && Auth::user()->role === 'kepala_bagian', function ($q) {
                // ANCHOR: Kepala bagian hanya bisa melihat disposisi yang ditujukan ke bagiannya
                $q->where('tujuan_bagian_id', Auth::user()->bagian_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $bagian = Bagian::where('status', 'Aktif')->get();

        // Collect filter values for form
        $filters = [
            'query' => $query,
            'status' => $status,
            'bagian_id' => $bagianId,
            'tanggal' => $tanggal,
            'sifat_surat' => $sifatSurat,
        ];

        return view('pages.disposisi.index', compact('disposisi', 'bagian', 'filters'));
    }

    /**
     * Display the specified disposisi.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $disposisi = Disposisi::with([
                'suratMasuk.tujuanBagian.kepalaBagian', 
                'tujuanBagian.kepalaBagian', 
                'user', 
                'creator', 
                'updater'
            ])->findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'Staf') {
                $isDisposisiKeBagiannya = $disposisi->tujuan_bagian_id === $user->bagian_id;
                $isDisposisiDariBagiannya = $disposisi->suratMasuk->tujuan_bagian_id === $user->bagian_id;
                
                if (!$isDisposisiKeBagiannya && !$isDisposisiDariBagiannya) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses untuk melihat disposisi ini.'
                        ], 403);
                    }
                    abort(403, 'Anda tidak memiliki akses untuk melihat disposisi ini.');
                }
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'disposisi' => $disposisi,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }
            
            return response()->json([
                'success' => true,
                'disposisi' => $disposisi
            ]);
            
        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * Update the specified disposisi in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $disposisi = Disposisi::findOrFail($id);
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'Staf') {
                $isDisposisiKeBagiannya = $disposisi->tujuan_bagian_id === $user->bagian_id;
                $isDisposisiDariBagiannya = $disposisi->suratMasuk->tujuan_bagian_id === $user->bagian_id;
                
                if (!$isDisposisiKeBagiannya && !$isDisposisiDariBagiannya) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses untuk mengedit disposisi ini.'
                        ], 403);
                    }
                    abort(403, 'Anda tidak memiliki akses untuk mengedit disposisi ini.');
                }
                
                // ANCHOR: Jika disposisi "ke bagiannya", hanya bisa edit status
                if ($isDisposisiKeBagiannya && !$isDisposisiDariBagiannya) {
                    $validated = $request->validate([
                        'status' => 'required|string|in:Menunggu,Dikerjakan,Selesai',
                    ], [
                        'status.required' => 'Status disposisi wajib dipilih.',
                        'status.in' => 'Status harus Menunggu, Dikerjakan, atau Selesai.',
                    ]);
                    
                    // ANCHOR: Hanya update status untuk disposisi "ke bagiannya"
                    $disposisi->update(['status' => $validated['status']]);
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Status disposisi berhasil diperbarui.',
                            'disposisi' => $disposisi->load(['suratMasuk.tujuanBagian.kepalaBagian', 'tujuanBagian.kepalaBagian', 'user', 'creator', 'updater']),
                            'timestamp' => now()->format('Y-m-d H:i:s')
                        ], 200);
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Status disposisi berhasil diperbarui.',
                        'disposisi' => $disposisi
                    ]);
                }
            }
            
            $validated = $request->validate([
                'tujuan_bagian_id' => 'required|exists:bagian,id',
                'status' => 'required|string|in:Menunggu,Dikerjakan,Selesai',
                'isi_instruksi' => 'required|string|min:10',
                'catatan' => 'nullable|string',
                'tanggal_disposisi' => 'nullable|date',
                'batas_waktu' => 'nullable|date|after_or_equal:tanggal_disposisi',
            ], [
                'tujuan_bagian_id.required' => 'Tujuan disposisi wajib dipilih.',
                'tujuan_bagian_id.exists' => 'Tujuan disposisi yang dipilih tidak valid.',
                'status.required' => 'Status disposisi wajib dipilih.',
                'status.in' => 'Status harus Menunggu, Dikerjakan, atau Selesai.',
                'isi_instruksi.required' => 'Instruksi disposisi wajib diisi.',
                'isi_instruksi.string' => 'Instruksi harus berupa teks.',
                'isi_instruksi.min' => 'Instruksi minimal 10 karakter.',
                'catatan.string' => 'Catatan harus berupa teks.',
                'tanggal_disposisi.date' => 'Format tanggal disposisi tidak valid.',
                'batas_waktu.date' => 'Format batas waktu tidak valid.',
                'batas_waktu.after_or_equal' => 'Batas waktu harus sama atau setelah tanggal disposisi.',
            ]);

            // ANCHOR: Audit fields (updated_by) are automatically handled by Auditable trait
            $disposisi->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disposisi berhasil diperbarui.',
                    'disposisi' => $disposisi->load(['suratMasuk.tujuanBagian.kepalaBagian', 'tujuanBagian.kepalaBagian', 'user', 'creator', 'updater']),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Disposisi berhasil diperbarui.',
                'disposisi' => $disposisi
            ]);

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
     * Remove the specified disposisi from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $disposisi = Disposisi::findOrFail($id);
            $disposisiId = $disposisi->id;
            
            // ANCHOR: Cek hak akses untuk staf
            $user = Auth::user();
            if ($user && $user->role === 'Staf') {
                $isDisposisiKeBagiannya = $disposisi->tujuan_bagian_id === $user->bagian_id;
                $isDisposisiDariBagiannya = $disposisi->suratMasuk->tujuan_bagian_id === $user->bagian_id;
                
                if (!$isDisposisiKeBagiannya && !$isDisposisiDariBagiannya) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses untuk menghapus disposisi ini.'
                        ], 403);
                    }
                    abort(403, 'Anda tidak memiliki akses untuk menghapus disposisi ini.');
                }
                
                // ANCHOR: Jika disposisi "ke bagiannya", tidak bisa hapus
                if ($isDisposisiKeBagiannya && !$isDisposisiDariBagiannya) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak dapat menghapus disposisi yang ditujukan ke bagian Anda.'
                        ], 403);
                    }
                    abort(403, 'Anda tidak dapat menghapus disposisi yang ditujukan ke bagian Anda.');
                }
            }
            
            $disposisi->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Disposisi berhasil dihapus.",
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => "Disposisi berhasil dihapus."
            ]);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }
}
