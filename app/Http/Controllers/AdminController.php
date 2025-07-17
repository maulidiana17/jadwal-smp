<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'admin');
        $admins = $query->paginate(10);

        return view('admin.index', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin'
        ]);

        return redirect()->route('admin.index')->with('success', 'Admin berhasil ditambahkan.');
    }

      public function update(Request $request, $id)
    {
        //$admin = User::findOrFail($id);
        $admin = User::findOrFail($request->user_id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6',
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $userData['password'] = bcrypt($request->password);
        }

        $admin->update($userData);


        return redirect()->route('admin.index')->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('admin.index')->with('success', 'Admin berhasil dihapus.');
    }

     public function kelas()
    {
        $kelas = DB::connection('mysql_spensa')
            ->table('kelas')
            ->orderBy('nama')
            ->get();

        return view('absensi.kelas', compact('kelas'));
    }
}
