<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * 1. Jalur API (Stateless - Pakai Token)
     * Digunakan untuk Mobile App atau aplikasi pihak ketiga.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Validasi: User ada, Password cocok, dan Status Aktif
        if (!$user || !Hash::check($request->password, $user->password) || $user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Kredensial salah atau akun Anda sedang dinonaktifkan.'],
            ]);
        }

        // Buat token baru (Sanctum)
        $token = $user->createToken('erp_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login API berhasil',
            'token'   => $token,
            'user'    => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    /**
     * 2. Jalur WEB (Stateful - Pakai Session/Cookie)
     * Khusus digunakan oleh halaman Blade & Alpine.js kamu.
     */
    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cari user dulu untuk cek status aktif sebelum attempt login
        $user = User::where('email', $request->email)->first();

        if ($user && $user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan oleh Admin.',
            ]);
        }

        // Proses login ke Session (Stateful)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate(); // Mencegah Session Fixation

            return redirect()->intended('/dashboard');
        }

        // Jika gagal total
        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Logout untuk API & Web
     */
    public function logout(Request $request)
    {
        // 1. Hapus token atau session yang aktif
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        // 2. Jika request berasal dari browser (Web), arahkan ke login
        // Jika dari API (Postman/Mobile), berikan respon JSON
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        // Pastikan session dibersihkan jika menggunakan web-based auth
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
