<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\AjaxErrorHandler;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use AjaxErrorHandler;
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('pages.autentikasi.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => ['required'],
                'password' => ['required'],
            ], [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            // Find user by username (automatically excludes soft deleted users due to SoftDeletes trait)
            $user = User::where('username', $validated['username'])->first();

            // Check if user exists, is not soft deleted, and password matches (hashed comparison)
            if ($user && \Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
                // Manually log in the user
                Auth::login($user);
                $request->session()->regenerate();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil! Selamat datang, ' . $user->nama . '.',
                    'redirect_url' => '/',
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);
            }

            return response()->json([
                'success' => false,
                'error_type' => 'general',
                'message' => 'Username atau password salah.'
            ], 401);

        } catch (\Exception $e) {
            // ANCHOR: Handle all types of errors using reusable trait
            return $this->handleAjaxError($request, $e);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}


