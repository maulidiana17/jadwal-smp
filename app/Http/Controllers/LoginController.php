<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    //
    public function index(){
        
        // dd(auth()->guard()->getName()); // biasanya "web"
        return view('auth.login');
    }

    public function login_proses(Request $request){
        // $request->validate([
        //     'email'     =>'required',
        //     'password'  =>'required',
        // ]);

        // $data = [
        //     'email'     => $request->email,
        //     'password'  => $request->password
        // ];

        // if(Auth::attempt($data)){
        //     return redirect('/dashboard');
        // } else{
        //     return redirect()->route('login')->with('failed','email atau password salah');
        // }
            // $request->validate([
            //     'email' => 'required|email',
            //     'password' => 'required',
            //     'role' => 'required|in:admin,guru,kurikulum',
            // ]);


        // if (Auth::attempt($request->only('email', 'password'))) {
        //     $user = auth()->user();

        //     if ($user->hasRole('admin')) {
        //         return redirect()->route('dashboard');
        //     } elseif ($user->hasRole('kurikulum')) {
        //         return redirect()->route('jadwal.index');
        //     } else {
        //         return abort(403, 'Role tidak diizinkan.');
        //     }
        // }
        
        if ($request->has('login_sebagai') && $request->login_sebagai === 'siswa') {
            // Login untuk siswa
            $request->validate([
                'nis' => 'required',
                'password' => 'required',
            ]);

            if (Auth::guard('siswa')->attempt(['nis' => $request->nis, 'password' => $request->password])) {
                $siswa = Auth::guard('siswa')->user();
                session(['siswa_nama' => $siswa->nama_lengkap]);
                return redirect('/dashboard');
            } else {
                return redirect('/')->with('warning', 'NIS atau password yang Anda masukkan salah.');
            }
        } else {
            // Login untuk user biasa (admin/guru/kurikulum)
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'role' => 'required|in:admin,guru,kurikulum',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $user = auth()->user();

                // Cek apakah role sesuai
                if (!$user->hasRole($request->role)) {
                    Auth::logout();
                    return redirect()->back()->with('error', 'Role tidak sesuai.');
                }

                // Redirect berdasarkan role
                switch ($request->role) {
                    case 'admin':
                        return redirect()->route('dashboard');
                    case 'kurikulum':
                        return redirect()->route('jadwal.index');
                    case 'guru':
                        return redirect()->route('dashboard.guru');
                    default:
                        return abort(403, 'Role tidak dikenali.');
                }
            }

            return redirect()->back()->with('error', 'Email atau password salah.');
        }
    }
        public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // arahkan ke route login
    }
}
