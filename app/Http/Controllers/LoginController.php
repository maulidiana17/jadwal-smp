<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    //
    public function index(){
        return view('auth.login');
        dd(auth()->guard()->getName()); // biasanya "web"
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

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = auth()->user();

            if ($user->hasRole('admin')) {
                return redirect()->route('dashboard');
            } elseif ($user->hasRole('kurikulum')) {
                return redirect()->route('jadwal.index');
            } else {
                return abort(403, 'Role tidak diizinkan.');
            }
        }

        return redirect()->back()->with('error', 'Email atau password salah.');

    }
        public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login'); // arahkan ke route login
    }
}
