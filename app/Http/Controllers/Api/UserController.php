<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Menampilkan daftar Admin/Staff (Sesuai Gambar 2 Figma)
    public function index()
    {
        $users = User::whereIn('role', ['super_admin', 'staff'])->get();
        return response()->json(['success' => true, 'data' => $users]);
    }

    // Menambah Staff Baru (Sesuai Gambar 3 Figma)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:staff,super_admin',
            'status' => 'required|in:active,inactive'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Staff berhasil ditambahkan', 'data' => $user], 201);
    }

    // Update Status atau Data Staff
    public function update(Request $request, User $user)
    {
        $request->validate([
            'status' => 'sometimes|in:active,inactive',
            'role' => 'sometimes|in:staff,super_admin'
        ]);

        $user->update($request->only(['name', 'email', 'role', 'status']));

        return response()->json(['success' => true, 'message' => 'Data staff diperbarui', 'data' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Staff berhasil dihapus']);
    }
}
