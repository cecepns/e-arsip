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
     * ANCHOR: Update institutional settings.
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
                'no_telp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'hapus_logo' => 'nullable|boolean',
            ], [
                'nama_instansi.required' => 'Nama instansi wajib diisi.',
                'nama_instansi.string' => 'Nama instansi harus berupa teks.',
                'nama_instansi.max' => 'Nama instansi maksimal 255 karakter.',
                'alamat.required' => 'Alamat instansi wajib diisi.',
                'alamat.string' => 'Alamat harus berupa teks.',
                'alamat.max' => 'Alamat maksimal 500 karakter.',
                'no_telp.string' => 'Nomor telepon harus berupa teks.',
                'no_telp.max' => 'Nomor telepon maksimal 20 karakter.',
                'email.email' => 'Email harus berupa alamat email yang valid.',
                'email.max' => 'Email maksimal 100 karakter.',
                'logo.image' => 'Logo harus berupa file gambar.',
                'logo.mimes' => 'Logo harus berupa file JPEG, PNG, JPG, atau GIF.',
                'logo.max' => 'Ukuran logo maksimal 2MB.',
                'hapus_logo.boolean' => 'Hapus logo harus berupa boolean.',
            ]);

            $pengaturan = Pengaturan::getInstance();
            $oldLogoPath = $pengaturan->logo;

            // ANCHOR: Handle logo upload and deletion logic
            $hapusLogo = $request->boolean('hapus_logo');
            $hasNewLogo = $request->hasFile('logo');

            // Delete old logo if needed (either uploading new or explicit delete)
            if (($hasNewLogo || $hapusLogo) && $oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            if ($hasNewLogo) {
                // Store new logo
                $validated['logo'] = $request->file('logo')->store('logos', 'public');
            } elseif ($hapusLogo) {
                $validated['logo'] = null;
            } else {
                // Keep existing logo if no new file uploaded and not deleting
                unset($validated['logo']);
            }

            $pengaturan->update($validated);

            // Prepare response data
            $responseData = [
                'success' => true,
                'message' => 'Pengaturan instansi berhasil diperbarui.',
                'pengaturan' => [
                    'nama_instansi' => $pengaturan->nama_instansi,
                    'alamat' => $pengaturan->alamat,
                    'no_telp' => $pengaturan->no_telp,
                    'email' => $pengaturan->email,
                    'logo' => $pengaturan->logo,
                ],
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            // Add logo URL if logo exists
            if ($pengaturan->logo) {
                $responseData['logo_url'] = Storage::url($pengaturan->logo);
            }

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }
}
