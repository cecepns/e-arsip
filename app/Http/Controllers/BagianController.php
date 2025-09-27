<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BagianController extends Controller
{
    // Tampilkan halaman manajemen bagian
    public function index(Request $request): View
    {
        $query = $request->get('search');
        
        $bagian = Bagian::with(['kepalaBagian', 'users', 'suratMasuk', 'suratKeluar'])
            ->when($query, function ($q) use ($query) {
                $q->where('nama_bagian', 'like', "%{$query}%")
                  ->orWhere('kepala_bagian', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.bagian.index', compact('bagian', 'query'));
    }

    // Simpan data bagian baru
    public function store(Request $request)
    {
        try {
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

            $bagian = Bagian::create($validated);

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bagian berhasil ditambahkan.',
                    'bagian' => $bagian,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 201);
            }

            return redirect()->route('bagian.index')
                ->with('success', 'Bagian berhasil ditambahkan.');

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
                    $errorMessage = 'Data tidak valid.';
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

    // Update data bagian
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

            $bagian->update($validated);

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Bagian '{$bagian->nama_bagian}' berhasil diperbarui.",
                    'bagian' => $bagian,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('bagian.index')
                ->with('success', "Bagian '{$bagian->nama_bagian}' berhasil diperbarui.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ANCHOR: Handle bagian not found
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bagian tidak ditemukan.',
                    'error_type' => 'not_found'
                ], 404);
            }
            throw $e;

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
                    $errorMessage = 'Data tidak valid.';
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

    // Hapus data bagian (soft delete)
    public function destroy(Request $request, $id)
    {
        try {
            $bagian = Bagian::findOrFail($id);
            $namaBagian = $bagian->nama_bagian;
            
            $bagian->delete();

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Bagian '{$namaBagian}' berhasil dihapus.",
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('bagian.index')
                ->with('success', "Bagian '{$namaBagian}' berhasil dihapus.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ANCHOR: Handle bagian not found
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bagian tidak ditemukan.',
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
                    $errorMessage = 'Bagian tidak dapat dihapus karena memiliki data terkait.';
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

    // Tampilkan detail bagian (opsional, untuk modal detail)
    public function show(Request $request, $id)
    {
        try {
            $bagian = Bagian::with(['users', 'suratMasuk', 'suratKeluar'])->findOrFail($id);
            
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

}
