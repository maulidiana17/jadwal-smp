<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // pastikan hanya user login yang bisa akses
    }

    /**
     * Tampilkan halaman setting profil.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('setting', compact('user'));
    }

    /**
     * Update data profil user.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        if ($request->hasFile('photo')) {
            // Hapus foto lama (opsional)
            if ($user->photo && \Storage::disk('public')->exists($user->photo)) {
                \Storage::disk('public')->delete($user->photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

}