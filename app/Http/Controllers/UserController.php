<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Traits\AjaxErrorHandler;

class UserController extends Controller
{
    use AjaxErrorHandler;
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $query = $request->get('search');
        
        $users = User::with('bagian')
            ->when($query, function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('nama', 'like', "%{$query}%")
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
                'nama' => 'required|string|max:100',
                'email' => 'required|email|max:100|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/',
                'role' => 'required|in:Admin,Staf',
                'bagian_id' => 'nullable|exists:bagian,id',
                'is_kepala_bagian' => 'nullable|boolean',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.string' => 'Username harus berupa teks.',
                'username.max' => 'Username maksimal 50 karakter.',
                'username.unique' => 'Username sudah digunakan.',
                'nama.required' => 'Nama wajib diisi.',
                'nama.string' => 'Nama harus berupa teks.',
                'nama.max' => 'Nama maksimal 100 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 100 karakter.',
                'email.unique' => 'Email sudah digunakan.',
                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.max' => 'Nomor telepon maksimal 20 karakter.',
                'password.required' => 'Password wajib diisi.',
                'password.string' => 'Password harus berupa teks.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol (@$!%*?&.).',
                'role.required' => 'Role wajib dipilih.',
                'role.in' => 'Role harus Admin atau Staf.',
                'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
                'is_kepala_bagian.boolean' => 'Status kepala bagian harus berupa boolean.',
            ]);

            // ANCHOR: Business logic - Admin tidak bisa memiliki bagian atau menjadi kepala bagian
            if ($validated['role'] === 'Admin') {
                $validated['bagian_id'] = null;
                $validated['is_kepala_bagian'] = false;
            }

            // ANCHOR: Business logic - hanya satu kepala bagian per bagian
            if (!empty($validated['is_kepala_bagian']) && !empty($validated['bagian_id'])) {
                // Reset kepala bagian lama di bagian yang sama
                \App\Models\Bagian::where('id', $validated['bagian_id'])
                    ->update(['kepala_bagian_user_id' => null]);
            }

            $user = User::create($validated);

            // ANCHOR: Set user sebagai kepala bagian jika checkbox dicentang
            if (!empty($validated['is_kepala_bagian']) && !empty($validated['bagian_id'])) {
                \App\Models\Bagian::where('id', $validated['bagian_id'])
                    ->update(['kepala_bagian_user_id' => $user->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.',
                'user' => $user->load('bagian'),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 201);            

        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
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
                'nama' => 'required|string|max:100',
                'email' => 'required|email|max:100|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'role' => 'required|in:Admin,Staf',
                'bagian_id' => 'nullable|exists:bagian,id',
                'is_kepala_bagian' => 'nullable|boolean',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.string' => 'Username harus berupa teks.',
                'username.max' => 'Username maksimal 50 karakter.',
                'username.unique' => 'Username sudah digunakan.',
                'nama.required' => 'Nama wajib diisi.',
                'nama.string' => 'Nama harus berupa teks.',
                'nama.max' => 'Nama maksimal 100 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 100 karakter.',
                'email.unique' => 'Email sudah digunakan.',
                'phone.string' => 'Nomor telepon harus berupa teks.',
                'phone.max' => 'Nomor telepon maksimal 20 karakter.',
                'password.string' => 'Password harus berupa teks.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol (@$!%*?&.).',
                'role.required' => 'Role wajib dipilih.',
                'role.in' => 'Role harus Admin atau Staf.',
                'bagian_id.exists' => 'Bagian yang dipilih tidak valid.',
                'is_kepala_bagian.boolean' => 'Status kepala bagian harus berupa boolean.',
            ]);

            // ANCHOR: Business logic - Admin tidak bisa memiliki bagian atau menjadi kepala bagian
            if ($validated['role'] === 'Admin') {
                $validated['bagian_id'] = null;
                $validated['is_kepala_bagian'] = false;
            }

            // ANCHOR: Business logic - hanya satu kepala bagian per bagian
            if (!empty($validated['is_kepala_bagian']) && !empty($validated['bagian_id'])) {
                // Reset kepala bagian lama di bagian yang sama (kecuali user yang sedang diedit)
                \App\Models\Bagian::where('id', $validated['bagian_id'])
                    ->where('kepala_bagian_user_id', '!=', $id)
                    ->update(['kepala_bagian_user_id' => null]);
            }

            // Jika password kosong atau null, hapus dari array validated untuk mempertahankan password lama
            if (empty($validated['password']) || $validated['password'] === '') {
                unset($validated['password']);
            }

            // Jika bagian_id kosong, set ke null
            if (empty($validated['bagian_id'])) {
                $validated['bagian_id'] = null;
            }

            $user->update($validated);

            // ANCHOR: Update kepala bagian di tabel bagian
            if (!empty($validated['is_kepala_bagian']) && !empty($validated['bagian_id'])) {
                // Set user sebagai kepala bagian
                \App\Models\Bagian::where('id', $validated['bagian_id'])
                    ->update(['kepala_bagian_user_id' => $user->id]);
            } else {
                // Reset kepala bagian jika checkbox tidak dicentang
                \App\Models\Bagian::where('kepala_bagian_user_id', $user->id)
                    ->update(['kepala_bagian_user_id' => null]);
            }

            // ANCHOR: Handle AJAX request
            return response()->json([
                'success' => true,
                'message' => "User '{$user->username}' berhasil diperbarui.",
                'user' => $user->load('bagian'),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 200);
    
        } catch (\Exception $e) {
            return $this->handleAjaxError($request, $e);
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
            
            // ANCHOR: Reset kepala bagian jika user yang dihapus adalah kepala bagian
            \App\Models\Bagian::where('kepala_bagian_user_id', $user->id)
                ->update(['kepala_bagian_user_id' => null]);
            
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