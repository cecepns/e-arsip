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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|max:50',
            'role' => 'required|in:Admin,Staf',
            'bagian_id' => 'nullable|exists:bagian,id',
        ]);

        User::create($validated);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'password' => 'nullable|string|max:50',
            'role' => 'required|in:Admin,Staf',
            'bagian_id' => 'nullable|exists:bagian,id',
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

        return redirect()->route('user.index')
            ->with('success', "User '{$user->username}' berhasil diperbarui.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $username = $user->username;
        
        $user->delete();

        return redirect()->route('user.index')
            ->with('success', "User '{$username}' berhasil dihapus.");
    }
}