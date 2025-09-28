<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Traits\AjaxErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    use AjaxErrorHandler;

    /**
     * Display the settings page.
     */
    public function index()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized access. Admin role required.');
        }

        $pengaturan = Pengaturan::getInstance();
        return view('pages.settings.index', compact('pengaturan'));
    }

    /**
     * Update institutional settings.
     */
    public function update(Request $request)
    {
        try {
            // Check if user is admin
            if (Auth::user()->role !== 'Admin') {
                return response()->json([
                    'success' => false,
                    'error_type' => 'general',
                    'message' => 'Unauthorized access. Admin role required.'
                ], 403);
            }

            $validated = $request->validate([
                'nama_instansi' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'nama_instansi.required' => 'Nama instansi wajib diisi.',
                'nama_instansi.max' => 'Nama instansi maksimal 255 karakter.',
                'alamat.required' => 'Alamat wajib diisi.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'logo.image' => 'Logo harus berupa file gambar.',
                'logo.mimes' => 'Logo harus berupa file JPEG, PNG, JPG, atau GIF.',
                'logo.max' => 'Ukuran logo maksimal 2MB.',
            ]);

            $pengaturan = Pengaturan::getInstance();

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($pengaturan->logo && Storage::disk('public')->exists($pengaturan->logo)) {
                    Storage::disk('public')->delete($pengaturan->logo);
                }

                // Store new logo
                $logoPath = $request->file('logo')->store('logos', 'public');
                $validated['logo'] = $logoPath;
            } else {
                // Keep existing logo if no new file uploaded
                unset($validated['logo']);
            }

            $pengaturan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil diperbarui.',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_type' => 'general',
                'message' => 'Terjadi kesalahan saat memperbarui pengaturan.'
            ], 500);
        }
    }
}
