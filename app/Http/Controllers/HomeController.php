<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use App\Models\Ruangan;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function dashboard(){
        return view('dashboardkurikulum', [
        'jumlahGuru' => Guru::count(),
        'jumlahRuangan' => Ruangan::count(),
        'jumlahKelas' => Kelas::count(),
        'jumlahMapel' => Mapel::count(),
    ]);

        }
    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    return '<a href="/user/'.$row->id.'/edit" class="btn btn-sm btn-primary">Edit</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function index(){

        $data = User::get();

       return view('user.index',compact('data'));
    }

    public function create(){
        return view('user.create');
    }

    public function store(Request $request){

         // Validasi jika perlu
    $validator = validator::make($request->all(),[
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:6',
        'role' => 'required',
    ]);

    // Simpan atau proses
    // User::create([...]);

    if ($validator->fails())return redirect()->back()->withInput()->withErrors($validator);

    $data['name']           =$request->name;
    $data['email']          =$request->email;
    $data['password']       =Hash::make($request->password);
    $data['role']           =$request->role;

    User::create($data);

    return redirect()->route('user.index')->with('success', 'user berhasil ditambahkan.');;
    }

    public function edit(Request $request, $id){
        $data = User::find($id);
        return view('user.edit',compact('data'));
        // dd($data);
    }

    public function update(Request $request, $id){
        // dd($requests->all());
             // Validasi jika perlu
    $validator = validator::make($request->all(),[
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'nullable',
        'role' => 'required',

    ]);

    // Simpan atau proses
    // User::create([...]);

    if ($validator->fails())return redirect()->back()->withInput()->withErrors($validator);

    $data['name']           =$request->name;
    $data['email']          =$request->email;
    if($request->password){
        $data['password']   =Hash::make($request->password);
    }
    $data['role']           =$request->role;


    User::whereId($id)->update($data);

    return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');;
    }

    public function delete(Request $request, $id){
        $data = User::find($id);

        if ($data){
            $data->delete();
        }

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');;
    }
}
