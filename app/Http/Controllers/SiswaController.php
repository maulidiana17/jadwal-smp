<?php

namespace App\Http\Controllers;

use App\Imports\SiswaImport;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index(Request $request) {
        $query = Siswa::query();
        $query->select('siswa.*');
        $query->orderBy('nama_lengkap');
        if(!empty($request->nama_siswa))
            {
                $query->where('nama_lengkap','like','%' . $request->nama_siswa . '%');
            }

         if ($request->status) {
            $query->where('siswa.status', $request->status);
        }
        $siswa = $query->paginate(5);

        return view('siswa.index', compact('siswa'));
    }

    public function store(Request $request) {
        $nis = $request->nis;
        $nama_lengkap = $request->nama_lengkap;
        $kelas = $request->kelas;
        $no_hp = $request->no_hp;
        $password = Hash::make($nis);
        // $siswa = DB::table('siswa')->where('nis', $nis)->first();
        if ($request->hasFile('foto')) {
            $foto = $nis . "." . $request->file('foto')->getClientOriginalExtension();
        }else {
            $foto = null;
        }

        try {
            $data = [
                'nis' => $nis,
                'nama_lengkap' => $nama_lengkap,
                'kelas' => $kelas,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password
            ];
            $simpan = DB::table('siswa')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/siswa/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['success' => 'Data Gagal Disimpan']);
        }

    }

     public function edit(Request $request)
    {
        $nis = $request->nis;
        $siswa = DB::table('siswa')->where('nis', $nis)->first();

        return view('siswa.edit', compact('siswa'));
    }

     public function update($nis, Request $request)
    {
        $nis = $request->nis;
        $nama_lengkap = $request->nama_lengkap;
        $kelas = $request->kelas;
        $no_hp = $request->no_hp;
        $status = $request->status;
        $password = Hash::make($nis);
        $old_foto = $request->old_foto;

        $siswa = DB::table('siswa')->where('nis', $nis)->first();

        if ($request->hasFile('foto')) {
            $foto = $nis . "." . $request->file('foto')->getClientOriginalExtension();
        }else {
            $foto = $old_foto;
        }


        if (!empty($request->password)) {
            $password = Hash::make($request->password);
        } else {
            $password = $siswa->password;
        }


        try {
            $data = [
                // 'nis' => $nis,
                'nama_lengkap' => $nama_lengkap,
                'kelas' => $kelas,
                'no_hp' => $no_hp,
                'foto' => $foto,
                'password' => $password,
                'status' => $status
            ];
            $update = DB::table('siswa')->where('nis',$nis)->update($data);
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/siswa/";
                    $folderPathOld = "public/uploads/siswa/" . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['success' => 'Data Gagal Diupdate']);
        }
    }

    public function delete($nis)
    {
        $delete = DB::table('siswa')->where('nis',$nis)->delete();
        if($delete){
            return Redirect::back()->with(['success'=>'Data Berhasil dihapus']);
        }else{
             return Redirect::back()->with(['success'=>'Data Gagal dihapus']);
        }

    }


//     public function aksiMassal(Request $request)
// {

//     $nisList = $request->nis;
//     $aksi = $request->aksi;

//     if (!$nisList || count($nisList) == 0) {
//         return back()->with('error', 'Pilih minimal satu siswa untuk diproses.');
//     }



//     foreach ($nisList as $nis) {
//         $siswa = DB::table('siswa')->where('nis', $nis)->first();
//         if (!$siswa) continue;

//         $kelas = strtoupper($siswa->kelas); // pastikan huruf besar
//         $tingkat = (int) substr($kelas, 0, 1); // angka pertama
//         $subkelas = strtoupper(substr($kelas, 1)); // huruf setelahnya

//         if ($aksi == 'naik' && $tingkat < 9) {
//             $kelasBaru = ($tingkat + 1) . $subkelas;

//             // Jalankan update
//     $updated = DB::table('siswa')->where('nis', $nis)->update(['kelas' => $kelasBaru]);

//             DB::table('siswa')->where('nis', $nis)->update(['kelas' => $kelasBaru]);
//         } elseif ($aksi == 'tinggal') {
//             continue;
//         } elseif ($aksi == 'lulus' && $tingkat == 9) {
//             DB::table('siswa')->where('nis', $nis)->delete();
//         }
//     }

//     return back()->with('success', 'Aksi berhasil dijalankan.');
// }
public function aksiMassal(Request $request)
{
    $nisList = $request->nis;
    $aksi = $request->aksi;

    if (!$nisList || count($nisList) == 0) {
        return back()->with('error', 'Pilih minimal satu siswa untuk diproses.');
    }

    foreach ($nisList as $nis) {
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        if (!$siswa) continue;

        $kelas = strtoupper(trim($siswa->kelas));
        $tingkat = (int) substr($kelas, 0, 1);
        $subkelas = strtoupper(substr($kelas, 1));

        if ($aksi == 'naik' && $tingkat < 9) {
            $kelasBaru = ($tingkat + 1) . $subkelas;

            DB::table('siswa')->where('nis', $nis)->update(['kelas' => $kelasBaru]);

        } elseif ($aksi == 'tinggal') {
            continue;

        } elseif ($aksi == 'lulus' && $tingkat == 9) {
            DB::table('siswa')->where('nis', $nis)->update(['status' => 'alumni']);
        }
    }

    return redirect()->route('siswa.index')->with('success', 'Aksi berhasil dijalankan.');
}

public function alumni()
{
    $siswa = Siswa::where('status', 'alumni')->paginate(20);
    return view('siswa.alumni', compact('siswa'));
}



    // public function aksiMassal(Request $request)
    // {
    //     $nisList = $request->nis;
    //     $aksi = $request->aksi;

    //     if (!$nisList || count($nisList) == 0) {
    //         return back()->with('error', 'Pilih minimal satu siswa untuk diproses.');
    //     }

    //     foreach ($nisList as $nis) {
    //         $siswa = DB::table('siswa')->where('nis', $nis)->first();
    //         if (!$siswa) continue;

    //         $kelas = $siswa->kelas;
    //         $tingkat = (int) substr($kelas, 0, 1);
    //         $subkelas = substr($kelas, 1);

    //         if ($aksi == 'naik' && $tingkat < 9) {
    //             $kelasBaru = ($tingkat + 1) . $subkelas;
    //             DB::table('siswa')->where('nis', $nis)->update(['kelas' => $kelasBaru]);
    //         } elseif ($aksi == 'tinggal') {
    //             continue;
    //         } elseif ($aksi == 'lulus' && $tingkat == 9) {
    //             DB::table('siswa')->where('nis', $nis)->delete();
    //         }
    //     }

    //     return back()->with('success', 'Aksi berhasil dijalankan.');
    // }

    public function importForm()
    {
        return view('siswa.import');
    }

    public function import(Request $request)
    {
        // Validasi file CSV
        $request->validate([
            'file' => 'required|mimes:csv,txt,xls,xlsx',
        ]);

        try {
        Excel::import(new SiswaImport, $request->file('file'));
        return redirect()->back()->with('success', 'Data siswa berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
