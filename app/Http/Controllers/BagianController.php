<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Traits\AjaxErrorHandler;

class BagianController extends Controller
{
    use AjaxErrorHandler;
    /**
     * ANCHOR Display a listing of the "Bagian".
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        
        $bagian = Bagian::with(['kepalaBagian', 'users'])
            ->when($query, function ($q) use ($query) {
                $q->where('nama_bagian', 'like', "%{$query}%")
                  ->orWhereHas('kepalaBagian', function ($userQuery) use ($query) {
                      $userQuery->where('nama', 'like', "%{$query}%")
                               ->orWhere('username', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $users = User::with('bagian')
            ->where('role', '!=', 'Admin')
            ->get();
        $kepalaBagianUserIds = $bagian->pluck('kepala_bagian_user_id')->filter()->unique();
        $usersNotMarkedAsKepalaBagian = $users->whereNotIn('id', $kepalaBagianUserIds)->values();
        return view('pages.bagian.index', compact('bagian', 'query', 'users', 'usersNotMarkedAsKepalaBagian'));
    }

    /**
     * ANCHOR: Store a newly created "Bagian".
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_bagian' => 'required|string|max:100|unique:bagian,nama_bagian',
                'kepala_bagian_user_id' => 'nullable|exists:users,id',
                'status' => 'required|in:Aktif,Nonaktif',
                'keterangan' => 'nullable|string',
            ], [
                'nama_bagian.required' => 'Nama bagian wajib diisi.',
                'nama_bagian.string' => 'Nama bagian harus berupa teks.',
                'nama_bagian.max' => 'Nama bagian maksimal 100 karakter.',
                'kepala_bagian_user_id.exists' => 'User kepala bagian tidak valid.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status harus Aktif atau Nonaktif.',
                'keterangan.string' => 'Keterangan harus berupa teks.',
                'nama_bagian.unique' => 'Nama bagian sudah ada.',
            ]);

            if (!empty($validated['kepala_bagian_user_id'])) {
                Bagian::where('kepala_bagian_user_id', $validated['kepala_bagian_user_id'])
                    ->update(['kepala_bagian_user_id' => null]);
            }

            $bagian = Bagian::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Bagian berhasil ditambahkan.',
                'bagian' => $bagian,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 201);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * ANCHOR: Update the specified "Bagian".
     */
    public function update(Request $request, $id)
    {
        try {
            $bagian = Bagian::findOrFail($id);

            $validated = $request->validate([
                'nama_bagian' => 'required|string|max:100',
                'kepala_bagian_user_id' => 'nullable|exists:users,id',
                'status' => 'required|in:Aktif,Nonaktif',
                'keterangan' => 'nullable|string',
            ], [
                'nama_bagian.required' => 'Nama bagian wajib diisi.',
                'nama_bagian.string' => 'Nama bagian harus berupa teks.',
                'nama_bagian.max' => 'Nama bagian maksimal 100 karakter.',
                'kepala_bagian_user_id.exists' => 'User kepala bagian tidak valid.',
                'status.required' => 'Status wajib dipilih.',
                'status.in' => 'Status harus Aktif atau Nonaktif.',
                'keterangan.string' => 'Keterangan harus berupa teks.',
            ]);

            if (!empty($validated['kepala_bagian_user_id'])) {
                Bagian::where('kepala_bagian_user_id', $validated['kepala_bagian_user_id'])
                    ->where('id', '!=', $id)
                    ->update(['kepala_bagian_user_id' => null]);
            }

            $bagian->update($validated);

            return response()->json([
                'success' => true,
                'message' => "Bagian '{$bagian->nama_bagian}' berhasil diperbarui.",
                'bagian' => $bagian,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * ANCHOR: Delete the specified "Bagian".
     */
    public function destroy(Request $request, $id)
    {
        try {
            $bagian = Bagian::findOrFail($id);
            $namaBagian = $bagian->nama_bagian;
            
            $bagian->delete();

            return response()->json([
                'success' => true,
                'message' => "Bagian '{$namaBagian}' berhasil dihapus.",
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * ANCHOR: Display the specified "Bagian".
     */
    public function show(Request $request, $id)
    {
        try {
            $bagian = Bagian::with(['kepalaBagian', 'users', 'suratMasuk', 'suratKeluar'])->findOrFail($id);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'bagian' => $bagian,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }
            
            return response()->json($bagian);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bagian tidak ditemukan.',
                    'error_type' => 'not_found'
                ], 404);
            }
            throw $e;
            
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
     * ANCHOR: Get surat data for specific bagian.
     */
    public function getSurat(Request $request, $id)
    {
        try {
            $bagian = Bagian::findOrFail($id);
            
            // Get surat masuk and surat keluar for this bagian
            $suratMasuk = \App\Models\SuratMasuk::where('tujuan_bagian_id', $id)
                ->select('id', 'nomor_surat', 'tanggal_surat', 'perihal', 'tujuan_bagian_id')
                ->orderBy('tanggal_surat', 'desc')
                ->limit(10)
                ->get();
                
            $suratKeluar = \App\Models\SuratKeluar::where('pengirim_bagian_id', $id)
                ->select('id', 'nomor_surat', 'tanggal_surat', 'perihal', 'pengirim_bagian_id')
                ->orderBy('tanggal_surat', 'desc')
                ->limit(10)
                ->get();

            // Combine and format data
            $suratData = collect();
            
            // Add surat masuk
            foreach ($suratMasuk as $surat) {
                $suratData->push([
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'perihal' => $surat->perihal,
                    'tujuan_bagian_id' => $surat->tujuan_bagian_id,
                    'jenis' => 'masuk'
                ]);
            }
            
            // Add surat keluar
            foreach ($suratKeluar as $surat) {
                $suratData->push([
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'perihal' => $surat->perihal,
                    'pengirim_bagian_id' => $surat->pengirim_bagian_id,
                    'jenis' => 'keluar'
                ]);
            }
            
            // Sort by date descending
            $suratData = $suratData->sortByDesc('tanggal_surat')->values();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'surat' => $suratData,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }
            
            return response()->json($suratData);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bagian tidak ditemukan.',
                    'error_type' => 'not_found'
                ], 404);
            }
            throw $e;
            
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
