<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use App\Traits\AjaxErrorHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    use AjaxErrorHandler;

    /**
     * ANCHOR: Show Profile Page
     * Display the user's profile page with their information
     */
    public function show()
    {
        $user = Auth::user();
        $bagian = Bagian::where('status', 'Aktif')->get();
        
        return view('pages.profile.index', compact('user', 'bagian'));
    }

    /**
     * ANCHOR: Update Profile Information
     * Update user's profile information (nama, email, phone)
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'nama' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'phone' => ['nullable', 'string', 'max:20'],
            ], [
                'nama.required' => 'Nama lengkap wajib diisi.',
                'nama.string' => 'Nama lengkap harus berupa teks.',
                'nama.max' => 'Nama lengkap maksimal 255 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 255 karakter.',
                'email.unique' => 'Email sudah digunakan oleh user lain.',
                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            ]);

            $user->update([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diperbarui.',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * ANCHOR: Update Password
     * Update user's password with validation
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            ], [
                'current_password.required' => 'Password lama wajib diisi.',
                'current_password.current_password' => 'Password lama tidak sesuai.',
                'password.required' => 'Password baru wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak sesuai.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.mixed_case' => 'Password harus mengandung huruf besar dan kecil.',
                'password.numbers' => 'Password harus mengandung angka.',
                'password.symbols' => 'Password harus mengandung simbol.',
            ]);

            $user->update([
                'password' => $validated['password'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui.',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
        }
    }
}
