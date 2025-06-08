<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    public function indexLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            LogAktivitas::create([
                'user_id' => $user->id,
                'aktivitas' => 'Telah login akun'
            ]);

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'mahasiswa') {
                return redirect()->route('mahasiswa.dashboard');
            } elseif ($user->role === 'dosen') {
                return redirect()->route('dosen.dashboard');
            } else {
                Alert::error('Gagal', 'Role anda tidak terdaftar');
                return back(); // default
            }
        }

        Alert::error('Gagal', 'Pengguna Tidak Terdaftar!');
        return back();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function profile()
    {
        $user = User::where('id', auth()->user()->id)->first();
        return view('auth.profile', [
            'title' => 'Profile',
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request, User $user)
    {
        // Validasi input
        $rules = [
            'nama' => 'required|string|max:255',
        ];

        $validated = $request->validate($rules);

        // Update data
        $user->nama = $validated['nama'];

        $user->save();

        Alert::success('Berhasil', 'Profile berhasil diperbarui.');
        return redirect()->back();
    }



    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_old' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'password_old.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        // Check if old password is correct
        if (!Hash::check($request->password_old, $user->password)) {
            Alert::error('Gagal', 'Password lama salah.');
            return back()->withInput();
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        Alert::success('Berhasil', 'Password berhasil diperbarui.');
        return redirect()->back();
    }
}
