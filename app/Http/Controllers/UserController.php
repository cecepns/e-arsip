<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        
        $users = User::with('bagian')
            ->when($query, function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $bagian = Bagian::where('status', 'Aktif')->get();

        return view('pages.user.index', compact('users', 'query', 'bagian'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:users,username',
                'email' => 'required|email|max:100|unique:users,email',
                'password' => 'required|string|max:50',
                'role' => 'required|in:Admin,Staf',
                'bagian_id' => 'nullable|exists:bagian,id',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.string' => 'Username harus berupa teks.',
                'username.max' => 'Username maksimal 50 karakter.',
                'username.unique' => 'Username sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 100 karakter.',
                'email.unique' => 'Email sudah digunakan.',
                'password.required' => 'Password wajib diisi.',
                'password.string' => 'Password harus berupa teks.',
                'password.max' => 'Password maksimal 50 karakter.',
                'role.required' => 'Role wajib dipilih.',
                'role.in' => 'Role harus Admin atau Staf.',
                'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
            ]);

            $user = User::create($validated);

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil ditambahkan.',
                    'user' => $user->load('bagian'),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 201);
            }

            return redirect()->route('user.index')
                ->with('success', 'User berhasil ditambahkan.');

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
                    $errorMessage = 'Data bagian tidak valid.';
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

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:users,username,' . $id,
                'email' => 'required|email|max:100|unique:users,email,' . $id,
                'password' => 'nullable|string|max:50',
                'role' => 'required|in:Admin,Staf',
                'bagian_id' => 'nullable|exists:bagian,id',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.string' => 'Username harus berupa teks.',
                'username.max' => 'Username maksimal 50 karakter.',
                'username.unique' => 'Username sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 100 karakter.',
                'email.unique' => 'Email sudah digunakan.',
                'password.string' => 'Password harus berupa teks.',
                'password.max' => 'Password maksimal 50 karakter.',
                'role.required' => 'Role wajib dipilih.',
                'role.in' => 'Role harus Admin atau Staf.',
                'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
            ]);

            // Jika password kosong atau null, hapus dari array validated untuk mempertahankan password lama
            if (empty($validated['password']) || $validated['password'] === '') {
                unset($validated['password']);
            }

            // Jika bagian_id kosong, set ke null
            if (empty($validated['bagian_id'])) {
                $validated['bagian_id'] = null;
            }

            $user->update($validated);

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User '{$user->username}' berhasil diperbarui.",
                    'user' => $user->load('bagian'),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('user.index')
                ->with('success', "User '{$user->username}' berhasil diperbarui.");

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
                    $errorMessage = 'Data bagian tidak valid.';
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

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $username = $user->username;
            
            $user->delete();

            // ANCHOR: Handle AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "User '{$username}' berhasil dihapus.",
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }

            return redirect()->route('user.index')
                ->with('success', "User '{$username}' berhasil dihapus.");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // ANCHOR: Handle user not found
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
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
                    $errorMessage = 'User tidak dapat dihapus karena memiliki data terkait.';
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
}