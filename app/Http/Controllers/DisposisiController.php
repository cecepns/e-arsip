<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Traits\AjaxErrorHandler;

class DisposisiController extends Controller
{
    use AjaxErrorHandler;

    /**
     * Display the specified disposisi.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $disposisi = Disposisi::with(['tujuanBagian', 'user', 'creator', 'updater'])->findOrFail($id);
            
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
            
            $validated = $request->validate([
                'tujuan_bagian_id' => 'required|exists:bagian,id',
                'status' => 'required|string|in:Menunggu,Dikerjakan,Selesai',
                'isi_instruksi' => 'required|string|min:10',
                'catatan' => 'nullable|string',
            ], [
                'tujuan_bagian_id.required' => 'Tujuan disposisi wajib dipilih.',
                'tujuan_bagian_id.exists' => 'Tujuan disposisi yang dipilih tidak valid.',
                'status.required' => 'Status disposisi wajib dipilih.',
                'status.in' => 'Status harus Menunggu, Dikerjakan, atau Selesai.',
                'isi_instruksi.required' => 'Instruksi disposisi wajib diisi.',
                'isi_instruksi.string' => 'Instruksi harus berupa teks.',
                'isi_instruksi.min' => 'Instruksi minimal 10 karakter.',
                'catatan.string' => 'Catatan harus berupa teks.',
            ]);

            // ANCHOR: Audit fields (updated_by) are automatically handled by Auditable trait
            $disposisi->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disposisi berhasil diperbarui.',
                    'disposisi' => $disposisi->load(['tujuanBagian', 'user', 'creator', 'updater']),
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
