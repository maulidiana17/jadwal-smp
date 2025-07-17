<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPresensiExport;
use App\Exports\RekapPresensiExport;
use App\Models\QRAbsen;
use App\Models\Siswa;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Controller\Absensi;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class AbsensiController extends Controller
{
    public function create() {
        $hariini = date("Y-m-d");
        $nis = Auth::guard('siswa')->user()->nis;
        $cek = DB::table('absensi')->where('tgl_absen', $hariini)->where('nis',$nis)->count();
        $lok_sekolah = DB::table('konfigurasi_lokasi')->where('id',1)->first();
        //dd($lokasi_sekolah);
        return view('absensi.create', compact('cek','lok_sekolah'));
    }

    public function store(Request $request){
        $nis = Auth::guard('siswa')->user()->nis;
        $tgl_absen = date("Y-m-d");
        $jam = date("H:i:s");
        $jamBuka  = "05:00:00";
        $jamTutup = "07:45:00";

        if ($jam < $jamBuka || $jam > $jamTutup) {
            echo "error|Presensi hanya diperbolehkan antara pukul 05:00 - 07:45|jam_invalid";
            return;
        }
        $lok_sekolah = DB::table('konfigurasi_lokasi')->where('id',1)->first();
        $lok = explode(",", $lok_sekolah->lokasi_sekolah);
        $latitudesekolah = $lok[0];
        $longitudesekolah = $lok[1];
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
        $jarak = $this->distance($latitudesekolah, $longitudesekolah, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);
        $lok_sekolah = DB::table('konfigurasi_lokasi')->where('id',1)->first();

        $cek = DB::table('absensi')->where('tgl_absen', $tgl_absen)->where('nis', $nis)->count();
        if($cek > 0) {
            $ket = "out";
        }else {
            $ket = "in";
        }

        $image = $request->image;
        $folderPath = "public/uploads/presensi/";
        $formatName = $nis . "-" . $tgl_absen . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($radius > $lok_sekolah->radius) {
            echo "error|Maaf Anda Berada Diluar Radius, Jarak Anda " . $radius . " meter dari sekolah!|radius";

        }else {
            if($cek > 0) {
                $data_pulang = [
                    'jam_keluar' => $jam,
                    // 'foto_keluar' => $fileName,
                    'location_keluar' => $lokasi
                ];
                $update = DB::table('absensi')->where('tgl_absen', $tgl_absen)->where('nis', $nis)->update($data_pulang);
                if ($update){
                    echo "success| Terimakasih, Hati-hati dijalan!|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Maaf Absen Gagal, Hubungi Tim IT|out";
                }
            }else {
                $siswa = DB::table('siswa')->where('nis', $nis)->first();
                $data = [
                    'nis' => $nis,
                    'nama_lengkap' => $siswa->nama_lengkap ?? null,
                    'kelas' => $siswa->kelas ?? null,
                    'tgl_absen' => $tgl_absen,
                    'jam_masuk' => $jam,
                    'foto_masuk' => $fileName,
                    'location_masuk' => $lokasi
                ];
                $simpan = DB::table('absensi')->insert($data);
                if ($simpan){
                    echo "success|Selamat, kamu sudah berhasil melakukan absensi!|in";
                    Storage::put($file, $image_base64);

                $jamBatas = '07:45:00';

                if ($jam <= $jamBatas) {
                    $pesan = "INFO PRESENSI SMPN 1 GENTENG:\nAnanda {$siswa->nama_lengkap} kelas {$siswa->kelas} telah melakukan absensi masuk pada pukul {$jam}.";
                } else {
                    $pesan = "INFO PRESENSI SMPN 1 GENTENG:\nAnanda {$siswa->nama_lengkap} kelas {$siswa->kelas} telah melakukan absensi masuk terlambat pada pukul {$jam}. Dimohon datang ke sekolah tepat waktu.";
                }
                $this->kirimWhatsapp($siswa->no_hp, $pesan);
                } else {
                    echo "error|Maaf Absen Gagal, Hubungi Admin|in";
                }
            }
        }

    }

    private function kirimWhatsapp($no_hp, $pesan)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array(
            'target' => $no_hp,
            'message' => $pesan,
            'countryCode' => '62'
        ),
        CURLOPT_HTTPHEADER => array(
            "Authorization: AJnBKGFUM4y17dZPFaKF"
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
    }

    //Untuk menghitung jarak radius
    function distance ($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile() {
        $nis = Auth::guard('siswa')->user()->nis;
        $siswa = DB::table('siswa')->where('nis', $nis)->first();
        return view('absensi.editprofile', compact('siswa'));
    }

    public function updateprofile(Request $request) {
        $nis = Auth::guard('siswa')->user()->nis;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $siswa = DB::table('siswa')->where('nis', $nis)->first();

        if ($request->hasFile('foto')) {
            $foto = $nis . "." . $request->file('foto')->getClientOriginalExtension();
        }else {
            $foto = $siswa->foto;
        }
        if(empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('siswa')->where('nis', $nis)->update($data);
        if($update){
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/siswa/";
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['success'=>'Data Berhasil di Update']);
        }else {
            return Redirect::back()->with(['error'=>'Data Gagal di Update']);
        }
    }
    public function histori (){

        $namabulan = ["", "Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

        return view('absensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request) {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nis = Auth::guard('siswa')->user()->nis;

        $histori = DB::table('absensi')
        ->where('nis', $nis)
        ->whereMonth('tgl_absen', $bulan)
        ->whereYear('tgl_absen', $tahun)
        ->orderBy('tgl_absen')
        ->paginate(5);
        // ->get();

        return view('absensi.gethistori', compact('histori'));
    }

//    public function showQrPresensi()
//     {
//         $qr = DB::table('qr_validasi')->where('tanggal', date('Y-m-d'))->first();

//         if (!$qr) {
//             // Generate baru jika belum ada untuk hari ini
//             $kode = "ABSEN-" . date('Ymd') . "-" . Str::random(5);
//             DB::table('qr_validasi')->insert([
//                 'tanggal' => date('Y-m-d'),
//                 'kode_qr' => $kode
//             ]);
//         } else {
//             $kode = $qr->kode_qr;
//         }

//         return view('absensi.qr-admin', compact('kode'));
//     }
public function showQrPresensi()
{
    $now = now();

    // Cari QR yang masih aktif
    $qr = DB::table('qr_validasi')
        ->where('tanggal', date('Y-m-d'))
        ->where('expired_at', '>', $now)
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$qr) {
        // Generate QR baru
        $kode = "ABSEN-" . date('Ymd-His') . "-" . Str::random(5);
        $expiredAt = $now->copy()->addSeconds(45);

        DB::table('qr_validasi')->insert([
            'tanggal'     => date('Y-m-d'),
            'kode_qr'     => $kode,
            'created_at'  => $now,
            'expired_at'  => $expiredAt
        ]);
    } else {
        $kode = $qr->kode_qr;
    }

    return view('absensi.qr-admin', compact('kode'));
}


//     public function getQrTerbaru()
// {
//     $now = now();

//     $qr = DB::table('qr_validasi')
//         ->where('tanggal', date('Y-m-d'))
//         ->where('expired_at', '>', $now)
//         ->orderBy('created_at', 'desc')
//         ->first();

//     if (!$qr) {
//         $kode = "ABSEN-" . date('Ymd-His') . "-" . Str::random(5);
//         $expiredAt = $now->copy()->addSeconds(45);

//         DB::table('qr_validasi')->insert([
//             'tanggal'     => date('Y-m-d'),
//             'kode_qr'     => $kode,
//             'created_at'  => $now,
//             'expired_at'  => $expiredAt
//         ]);
//     } else {
//         $kode = $qr->kode_qr;
//     }

//     return response()->json(['kode' => $kode]);
// }
public function getQrTerbaru()
{
    $now = now();
    $jamSekarang = $now->format('H:i');

    // Atur waktu buka dan tutup presensi
    // $jamBuka  = '05:00';
    // $jamTutup = '07:45';
    $jamBuka  = '05:00';
    $jamTutup = '21:45';

    // Cek apakah sekarang di luar jam presensi
    if ($jamSekarang < $jamBuka || $jamSekarang > $jamTutup) {
        return response()->json([
            'kode' => null,
            'aktif' => false,
            'pesan' => 'Presensi hanya dibuka pukul 05:00 - 07:45'
        ]);
    }

    // Ambil QR terbaru yang belum expired
    $qr = DB::table('qr_validasi')
        ->where('tanggal', date('Y-m-d'))
        ->where('expired_at', '>', $now)
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$qr) {
        $kode = "ABSEN-" . date('Ymd-His') . "-" . Str::random(5);
        $expiredAt = $now->copy()->addSeconds(45);

        DB::table('qr_validasi')->insert([
            'tanggal'     => date('Y-m-d'),
            'kode_qr'     => $kode,
            'created_at'  => $now,
            'expired_at'  => $expiredAt
        ]);
    } else {
        $kode = $qr->kode_qr;
    }

    return response()->json([
        'kode' => $kode,
        'aktif' => true
    ]);
}



    // public function displayQr($token)
    // {
    //     $tokenValid = 'qrcodeSMPN1GENTENG';

    //     if ($token !== $tokenValid) {
    //         abort(403, 'Akses Ditolak');
    //     }

    //     $qr = DB::table('qr_validasi')->where('tanggal', date('Y-m-d'))->first();

    //     if (!$qr) {
    //         return "QR Code belum digenerate oleh admin.";
    //     }

    //     $kode = $qr->kode_qr;
    //     return view('absensi.qr-display', compact('kode'));
    // }
    public function displayQr($token)
{
    $tokenValid = 'qrcodeSMPN1GENTENG';

    if ($token !== $tokenValid) {
        abort(403, 'Akses Ditolak');
    }

    return view('absensi.qr-display');
}




    public function izin(){
        $nis = Auth::guard('siswa')->user()->nis;
        $dataizin = DB::table('pengajuan_izin')->where('nis', $nis)->get();
        return view('absensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('absensi.buatizin');
    }



public function storeizin(Request $request)
{
    $request->validate([
        'tanggal_izin' => 'required|date',
        'tanggal_izin_akhir' => 'required|date|after_or_equal:tanggal_izin',
        'status' => 'required',
        'keterangan' => 'required',
        'file_surat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // tambahkan validasi file
    ]);

    $nis = Auth::guard('siswa')->user()->nis;
    $siswa = DB::table('siswa')->where('nis', $nis)->first();

    $fileName = null;
    if ($request->hasFile('file_surat')) {
        $file = $request->file('file_surat');
        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        // $file->storeAs('public/surat', $fileName);
        $file->storeAs('public/uploads/surat', $fileName);

    }

    $data = [
        'nis' => $nis,
        'nama_lengkap' => $siswa->nama_lengkap ?? null,
        'kelas' => $siswa->kelas ?? null,
        'tanggal_izin' => $request->tanggal_izin,
        'tanggal_izin_akhir' => $request->tanggal_izin_akhir,
        'status' => $request->status,
        'keterangan' => $request->keterangan,
        'file_surat' => $fileName, // tambahkan ke data insert
        'status_approved' => 0,    // default belum disetujui
        'catatan_penolakan' => null
    ];

    $simpan = DB::table('pengajuan_izin')->insert($data);

    if($simpan){
        return redirect('/absensi/izin')->with('success', 'Pengajuan berhasil. Silakan cek status dan unggah surat jika belum. Surat menyusul dan segera diserahkan ke admin sekolah.');
    } else {
        return redirect('/absensi/izin')->with('error', 'Data gagal disimpan.');
    }
}

public function editizin($id)
{
    $izin = DB::table('pengajuan_izin')->where('id', $id)->first();

//     dd([
//     'izin_nis' => $izin->nis,
//     'auth_nis' => Auth::guard('siswa')->user()->nis,
// ]);

    // Pastikan siswa hanya bisa edit miliknya sendiri
    // if (!$izin || $izin->nis !== Auth::guard('siswa')->user()->nis) {
    //     abort(403, 'Akses ditolak.');
    // }
    if (!$izin || (string)$izin->nis !== (string)Auth::guard('siswa')->user()->nis) {
    abort(403, 'Akses ditolak.');
}


    return view('absensi.editizin', compact('izin'));
}

public function updateizin(Request $request)
{
    $request->validate([
        'id' => 'required|exists:pengajuan_izin,id',
        'keterangan' => 'required|string',
        'file_surat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $izin = DB::table('pengajuan_izin')->where('id', $request->id)->first();

    if (!$izin || (string)$izin->nis !== (string)Auth::guard('siswa')->user()->nis) {
    abort(403, 'Akses ditolak.');
}


    $data = [
        'keterangan' => $request->keterangan,
        'status_approved' => 0,
        'catatan_penolakan' => null
    ];

    if ($request->hasFile('file_surat')) {
        $file = $request->file('file_surat');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/uploads/surat', $fileName);
        $data['file_surat'] = $fileName;
    }

    DB::table('pengajuan_izin')->where('id', $request->id)->update($data);

    return redirect('/absensi/izin')->with('success', 'Pengajuan berhasil diperbarui. Menunggu verifikasi ulang.');
}

    //benar
    // public function storeizin(Request $request)
    // {
    //     $request->validate([
    //         'tanggal_izin' => 'required|date',
    //         'tanggal_izin_akhir' => 'required|date|after_or_equal:tanggal_izin',
    //         'status' => 'required',
    //         'keterangan' => 'required',

    //     ]);

    //     $nis = Auth::guard('siswa')->user()->nis;
    //     $siswa = DB::table('siswa')->where('nis', $nis)->first();

    //     $data = [
    //         'nis' => $nis,
    //         'nama_lengkap' => $siswa->nama_lengkap ?? null,
    //         'kelas' => $siswa->kelas ?? null,
    //         'tanggal_izin' => $request->tanggal_izin,
    //         'tanggal_izin_akhir' => $request->tanggal_izin_akhir,
    //         'status' => $request->status,
    //         'keterangan' => $request->keterangan,
    //     ];

    //     $simpan = DB::table('pengajuan_izin')->insert($data);
    //     if($simpan){
    //         return redirect('/absensi/izin')->with('success', 'Pengajuan berhasil. Segera serahkan surat izin/sakit ke admin sekolah.');
    //     }else {
    //         return redirect('/absensi/izin')->with('error', 'Data gagal disimpan.');
    //     }
    // }

    // public function scan()
    // {
    //     return view('absensi.scan');
    // }
    public function scan()
    {
        $user = Auth::guard('siswa')->user();
        $tanggal = date('Y-m-d');

        $izin = DB::table('pengajuan_izin')
            ->where('nis', $user->nis)
            ->where('tanggal_izin', $tanggal)
            ->where('status_approved', 1)
            ->first();

        if ($izin && in_array($izin->status, ['i', 's'])) {
            return redirect('/dashboard')->with('error', 'Kamu sedang izin atau sakit hari ini, tidak bisa absen.');

        }

        return view('absensi.scan');
    }


    public function simpanScanQR(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string',
            'nip' => 'required|string',
            'mapel' => 'required|string',
        ]);

        $nis = Auth::guard('siswa')->user()->nis;
        $siswa = DB::table('siswa')->where('nis', $nis)->first();

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $sudahAbsen = QRAbsen::where('nis', $siswa->nis)
            ->where('nip', $request->nip)
            ->where('mapel', $request->mapel)
            ->whereDate('waktu', now()->toDateString())
            ->exists();

        if ($sudahAbsen) {
            return redirect()->back()->with('error', 'Anda sudah melakukan scan QR untuk mata pelajaran ini hari ini.');
        }

        $absen = QRAbsen::create([
            'nis' => $siswa->nis,
            'nama' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelas,
            'nip' => $request->nip,
            'mapel' => $request->mapel,
            'waktu' => now(),
        ]);

        session()->put('siswa', [
            'nis' => $siswa->nis,
            'nama' => $siswa->nama_lengkap,
            'kelas' => $siswa->kelas,
            'waktu' => $absen->waktu,
        ]);

        return redirect()->route('absensi.success');
    }

    public function getStatusHadir(Request $request)
    {
        $user = Auth::guard('siswa')->user();
        $nis = $user->nis ?? null;
        $mapel = $request->query('mapel');

        if (!$nis || !$mapel) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }

        $tanggal = now()->toDateString();

        $sudahHadir = QRAbsen::where('nis', $nis)
            ->where('mapel', $mapel)
            ->whereDate('waktu', $tanggal)
            ->exists();

        return response()->json(['hadir' => $sudahHadir]);
    }
    // public function getStatusHadir(Request $request)
    // {
    //     $user = Auth::guard('siswa')->user();
    //     $nis = $user->nis ?? null;
    //     $mapel = $request->query('mapel');

    //     if (!$nis || !$mapel) {
    //         return response()->json(['error' => 'Data tidak lengkap'], 400);
    //     }

    //     $tanggal = now()->toDateString();

    //     // Cek apakah sudah absen
    //     $sudahHadir = QRAbsen::where('nis', $nis)
    //         ->whereRaw('LOWER(mapel) = ?', [strtolower($mapel)])
    //        // ->where('mapel', $mapel)
    //         ->whereDate('waktu', $tanggal)
    //         ->exists();

    //     // Cek apakah izin atau sakit hari ini
    //     $izin = DB::table('pengajuan_izin')
    //         ->where('nis', $nis)
    //         ->where('tanggal_izin', $tanggal)
    //         ->where('status_approved', 1)
    //         ->first();

    //     return response()->json([
    //         'hadir' => $sudahHadir,
    //         'izin' => $izin && $izin->status === 'i' ? true : false,
    //         'sakit' => $izin && $izin->status === 's' ? true : false,
    //     ]);
    // }


    public function success()
    {
        return view('absensi.success');
    }

    public function monitoring()
    {
        return view('absensi.monitoring');
    }

    public function getabsensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $absensi = DB::table('absensi')
        //->select('absensi.*','nama_lengkap','kelas')
        ->select('absensi.*', 'siswa.nama_lengkap', 'siswa.kelas')
        ->join('siswa', 'absensi.nis' ,'=' ,'siswa.nis')
        ->where('tgl_absen', $tanggal)
        ->paginate(50);
        // ->get();


        return view('absensi.getabsensi', compact('absensi'));
    }


    // public function getNotifikasi()
    // {
    //     $data = DB::table('pengajuan_izin')
    //         ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
    //         ->where('pengajuan_izin.status_approved', '0')
    //         ->orderBy('pengajuan_izin.tanggal_izin', 'desc')
    //         ->select(
    //             'siswa.nama_lengkap',
    //             'pengajuan_izin.tanggal_izin',
    //             DB::raw("CASE
    //                         WHEN pengajuan_izin.status = 'i' THEN 'izin'
    //                         WHEN pengajuan_izin.status = 's' THEN 'sakit'
    //                         ELSE 'lainnya'
    //                     END as jenis_pengajuan")
    //         )
    //         ->get();

    //     return response()->json($data);
    // }
    public function getNotifikasi()
{
    $data = DB::table('pengajuan_izin')
        ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
        ->whereIn('pengajuan_izin.status_approved', [0, 2]) // Menunggu & Ditolak
        ->orderBy('pengajuan_izin.tanggal_izin', 'desc')
        ->select(
            'pengajuan_izin.id',
            'siswa.nama_lengkap',
            'pengajuan_izin.tanggal_izin',
            'pengajuan_izin.status_approved',
            'pengajuan_izin.file_surat',
            DB::raw("CASE
                        WHEN pengajuan_izin.status = 'i' THEN 'izin'
                        WHEN pengajuan_izin.status = 's' THEN 'sakit'
                        ELSE 'lainnya'
                    END as jenis_pengajuan")
        )
        ->get();

    return response()->json($data);
}

    public function maps(Request $request)
    {
        $tanggal = $request->tanggal;

        $absensi = DB::table('absensi')
            ->join('siswa', 'absensi.nis', '=', 'siswa.nis')
            ->select(
                'absensi.id',
                'absensi.nis',
                'siswa.nama_lengkap',
                'siswa.kelas',
                'absensi.location_masuk',
                'absensi.location_keluar',
                'absensi.tgl_absen',
                'absensi.jam_masuk',
                'absensi.jam_keluar'
            )
            ->where('tgl_absen', $tanggal)
            ->whereNotNull('location_masuk')
            ->get();

        return view('absensi.allmap', compact('absensi', 'tanggal'));
    }

    public function showmap(Request $request)
    {
        $id = $request->id;
        $absensi = DB::table('absensi')->where('id', $id)->join('siswa', 'absensi.nis', '=', 'siswa.nis')->first();

        return view('absensi.showmap', compact('absensi'));
    }


    public function laporan()
    {
        $namabulan = ["", "Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

        $kelasData = DB::table('siswa')
        ->select('kelas')
        ->distinct()
        ->orderByRaw("
            FIELD(SUBSTRING_INDEX(kelas, ' ', 1), '7', '8', '9'),
            SUBSTRING_INDEX(kelas, ' ', -1)
        ")
        ->get()
        ->pluck('kelas');

        $kelasGroup = [];

        //foreach ($kelasData as $kelas) {
        //   $tingkat = explode(' ', $kelas)[0];
        //  if (!isset($kelasGroup[$tingkat])) {
        //     $kelasGroup[$tingkat] = [];
        // }
        // $kelasGroup[$tingkat][] = $kelas;
        // }
        foreach ($kelasData as $kelas) {
            $tingkat = substr($kelas, 0, 1); // Ambil angka tingkat: 7, 8, atau 9
            $label = "Kelas $tingkat";
            if (!isset($kelasGroup[$label])) {
                $kelasGroup[$label] = [];
            }
            $kelasGroup[$label][] = $kelas;
        }
        $siswa = DB::table('siswa')->orderBy('nama_lengkap')->get();

        return view('absensi.laporan', compact('namabulan', 'siswa','kelasGroup'));
    }


    public function cetaklaporan(Request $request)
    {
        $nis = $request->input('nis');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $namabulan = ["", "Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

        $siswa = DB::table('siswa')->where('nis', $nis)->first();

        $absensi = DB::table('absensi')
            ->where('nis', $nis)
            ->whereYear('tgl_absen', $tahun)
            ->whereMonth('tgl_absen', $bulan)
            ->get()
            ->keyBy('tgl_absen');

        $izinSakit = DB::table('pengajuan_izin')
            ->where('nis', $nis)
            ->where('status_approved', 1)
            ->where(function($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal_izin', $bulan)
                ->whereYear('tanggal_izin', $tahun)
                ->orWhereMonth('tanggal_izin_akhir', $bulan)
                ->whereYear('tanggal_izin_akhir', $tahun);
            })
            ->get();

        // Gabung data absensi & izin
        $tanggalData = [];

        foreach ($absensi as $a) {
            $tanggalData[$a->tgl_absen] = [
                'tgl' => $a->tgl_absen,
                'nis' => $a->nis,
                'jam_masuk' => $a->jam_masuk,
                'jam_keluar' => $a->jam_keluar,
                'status' => 'absen'
            ];
        }

        foreach ($izinSakit as $izin) {
            $start = $izin->tanggal_izin;
            $end = $izin->tanggal_izin_akhir ?: $izin->tanggal_izin;

            $periode = new DatePeriod(
                new DateTime($start),
                new DateInterval('P1D'),
                (new DateTime($end))->modify('+1 day')
            );

            foreach ($periode as $date) {
                $tgl = $date->format('Y-m-d');
                if (!isset($tanggalData[$tgl])) {
                    $tanggalData[$tgl] = [
                        'tgl' => $tgl,
                        'nis' => $nis,
                        'status' => $izin->status == 'i' ? 'izin' : 'sakit'
                    ];
                }
            }
        }

        ksort($tanggalData);

        // ✅ Tambahkan fitur export ke Excel
        if ($request->has('exportexcel')) {
            $nama_siswa = str_replace(' ', '_', $siswa->nama_lengkap);

            return Excel::download(new \App\Exports\LaporanPresensiExport($siswa, $tanggalData, $bulan, $tahun, $namabulan),
            "Laporan_Presensi_{$nama_siswa}.xlsx");
        }

        // Jika bukan export, tampilkan ke view biasa
        return view('absensi.cetaklaporan', [
            'siswa' => $siswa,
            'tanggalData' => $tanggalData,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namabulan' => $namabulan
        ]);
    }

    public function getSiswaByKelas($kelas)
    {
        $siswa = DB::table('siswa')
            ->where('kelas', $kelas)
            ->orderBy('nama_lengkap')
            ->get();

        return response()->json($siswa);
    }

    public function rekap()
    {
        $namabulan = ["", "Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        return view('absensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $kelas = $request->kelas;
        $namabulan = ["", "Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $query = DB::table('absensi')
            ->selectRaw(
                'absensi.nis,
                siswa.nama_lengkap,
                siswa.kelas,' .
                collect(range(1, $jumlahHari))->map(function ($day) {
                    return "MAX(IF(DAY(tgl_absen) = $day, CONCAT(jam_masuk, '-', IFNULL(jam_keluar, '00:00:00')), '')) AS tgl_$day";
                })->implode(', ') . ',
                SUM(CASE WHEN jam_masuk IS NOT NULL THEN 1 ELSE 0 END) AS jumlah_hadir,
                SUM(CASE WHEN jam_masuk > "07:45:00" THEN 1 ELSE 0 END) AS jumlah_terlambat'
            )
            ->join('siswa', 'absensi.nis', '=', 'siswa.nis')
            ->whereMonth('tgl_absen', $bulan)
            ->whereYear('tgl_absen', $tahun);

            if ($kelas) {
                $query->where('siswa.kelas', $kelas);
            }

            $rekap = $query->groupBy('absensi.nis', 'siswa.nama_lengkap', 'siswa.kelas')->get();

            $izinSakit = DB::table('pengajuan_izin')
                ->select('nis', 'status', DB::raw('SUM(DATEDIFF(tanggal_izin_akhir, tanggal_izin) + 1) as jumlah'))
                ->where('status_approved', 1)
                ->whereMonth('tanggal_izin', $bulan)
                ->whereYear('tanggal_izin', $tahun)
                ->groupBy('nis', 'status')
                ->get()
                ->groupBy('nis')
                ->map(function ($items) {
                    $result = [];
                    foreach ($items as $item) {
                        $kode = $item->status === 'i' ? 'Izin' : 'Sakit';
                        $result[] = $item->jumlah . ' ' . $kode;
                    }
                    return implode(', ', $result);
                });
            // Rekap izin/sakit 6 bulan terakhir
            $startDate = now()->subMonths(6)->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();

            $izinSakitTotal6Bulan = DB::table('pengajuan_izin')
                ->select('nis', 'status', DB::raw('SUM(DATEDIFF(tanggal_izin_akhir, tanggal_izin) + 1) as jumlah'))
                ->where('status_approved', 1)
                ->whereBetween('tanggal_izin', [$startDate, $endDate])
                ->groupBy('nis', 'status')
                ->get()
                ->groupBy('nis')
                ->map(function ($items) {
                    $totalIzin = 0;
                    $totalSakit = 0;
                    foreach ($items as $item) {
                        if ($item->status === 'i') {
                            $totalIzin += $item->jumlah;
                        } elseif ($item->status === 's') {
                            $totalSakit += $item->jumlah;
                        }
                    }

                    $detail = [];
                    if ($totalIzin > 0) $detail[] = $totalIzin . ' Izin';
                    if ($totalSakit > 0) $detail[] = $totalSakit . ' Sakit';

                    return [
                        'izin' => $totalIzin,
                        'sakit' => $totalSakit,
                        'total' => $totalIzin + $totalSakit,
                        'detail' => implode(', ', $detail)
                    ];
                });


            // Gabungkan data izin ke dalam rekap utama
            foreach ($rekap as $data) {
                $data->izin_sakit_detail = $izinSakit[$data->nis] ?? '-';

                $total6bln = $izinSakitTotal6Bulan[$data->nis] ?? ['izin' => 0, 'sakit' => 0, 'total' => 0, 'detail' => '-'];
                $data->izin_6bulan = $total6bln['izin'];
                $data->sakit_6bulan = $total6bln['sakit'];
                $data->total_izin_sakit_6bulan = $total6bln['total'];
                $data->total_izin_sakit_6bulan_detail = $total6bln['detail'];
            }

            $izinPerTanggal = DB::table('pengajuan_izin')
                ->select('nis', 'tanggal_izin', 'status')
                ->where('status_approved', 1)
                ->whereMonth('tanggal_izin', $bulan)
                ->whereYear('tanggal_izin', $tahun)
                ->get()
                ->groupBy('nis')
                ->map(function ($items) {
                    return collect($items)->keyBy(function ($item) {
                        return $item->tanggal_izin;
                    });
                });

            // Daftar tanggal libur daerah (Bali) yang bukan libur nasional
            $liburDaerahBali = [
                '2025-04-22', // Penampahan Galungan
                '2025-04-23', // Hari Raya Galungan
                '2025-04-24', // Umanis Galungan
                '2025-01-27', // Hari Siwa Ratri
                '2025-02-8',  // Hari Saraswati"
                '2025-04-1',  // Hari Raya Idul Fitri 1446 Hijriyah
                '2025-05-3',  // Hari Raya Kuningan
                '2025-09-6',  // Hari Saraswati
                '2025-11-29', // Hari Raya Kuningan
                '2025-11-20', // Umanis Galungan
                '2025-11-19', // Hari Raya Galungan
                '2025-11-18', // Penampahan Galungan
            ];

            $tanggalMerah = [];

            $response = Http::get("https://api-harilibur.vercel.app/api");
            if ($response->successful()) {
                foreach ($response->json() as $libur) {
                    $tgl = $libur['holiday_date'];
                    $desc = $libur['holiday_name'];
                    if (
                        date('Y', strtotime($tgl)) == $tahun &&
                        date('m', strtotime($tgl)) == str_pad($bulan, 2, '0', STR_PAD_LEFT)
                    ) {
                        if (!in_array($tgl, $liburDaerahBali)) {
                            $tanggalMerah[$tgl] = $desc;
                        }
                    }
                }
            }

              $rentangIzinSakit = DB::table('pengajuan_izin')
                ->select('nis', DB::raw('MIN(tanggal_izin) as tanggal_awal'), DB::raw('MAX(tanggal_izin) as tanggal_akhir'))
                ->where('status_approved', 1)
                ->whereMonth('tanggal_izin', $bulan)
                ->whereYear('tanggal_izin', $tahun)
                ->groupBy('nis')
                ->get()
                ->keyBy('nis');
            $izinRentangPerSiswa = DB::table('pengajuan_izin')
                ->select('nis', 'tanggal_izin as tanggal_awal', 'tanggal_izin_akhir', 'status')
                ->where('status_approved', 1)
                ->where(function($q) use ($bulan, $tahun) {
                    $q->whereMonth('tanggal_izin', $bulan)
                    ->whereYear('tanggal_izin', $tahun)
                    ->orWhereMonth('tanggal_izin_akhir', $bulan)
                    ->whereYear('tanggal_izin_akhir', $tahun);
                })
                ->get()
                ->groupBy('nis');



            for ($i = 1; $i <= $jumlahHari; $i++) {
                $tgl = sprintf('%04d-%02d-%02d', $tahun, $bulan, $i);
                if (date('w', strtotime($tgl)) == 0 && !isset($tanggalMerah[$tgl])) {
                    $tanggalMerah[$tgl] = 'Hari Minggu';
                }
            }

            if ($request->has('exportexcel')) {
                $namaBulan = $namabulan[intval($bulan)];
                $namaKelas = $kelas ? "Kelas $kelas" : "Semua Kelas";
                $filename = "Rekap Presensi Siswa $namaKelas $namaBulan $tahun.xlsx";
                 return Excel::download(new RekapPresensiExport(
                    $rekap,
                    $jumlahHari,
                    $izinPerTanggal,
                    $tanggalMerah,
                    $tahun,
                    $bulan,
                    $izinRentangPerSiswa
                ), $filename);
            }
              //  dd($tanggalMerah);
            return view('absensi.cetakrekap', compact('bulan', 'tahun', 'rekap', 'kelas', 'namabulan', 'jumlahHari', 'tanggalMerah', 'izinSakit', 'izinPerTanggal', 'rentangIzinSakit', 'izinRentangPerSiswa'));
    }

        // public function izinsakit(Request $request)
        // {
        //     $query = DB::table('pengajuan_izin')
        //     ->join('siswa','pengajuan_izin.nis', '=', 'siswa.nis')
        //     ->orderBy('tanggal_izin','desc');
        //      if (!empty($request->nama_siswa)) {
        //             $query->where('siswa.nama_lengkap', 'like', '%' . $request->nama_siswa . '%');
        //         }

        //     $izinsakit = $query->paginate(5);
        //     return view('absensi.izinsakit', compact('izinsakit'));
        // }
        public function izinsakit(Request $request)
        {
            $query = DB::table('pengajuan_izin')
                ->join('siswa', 'pengajuan_izin.nis', '=', 'siswa.nis')
                ->select(
                    'pengajuan_izin.*',
                    'siswa.nama_lengkap',
                    'siswa.kelas'
                )
                ->orderBy('tanggal_izin', 'desc');

            if (!empty($request->nama_siswa)) {
                $query->where('siswa.nama_lengkap', 'like', '%' . $request->nama_siswa . '%');
            }

            $izinsakit = $query->paginate(5);

            return view('absensi.izinsakit', compact('izinsakit'));
        }


        public function approvedizinsakit(Request $request)
        {
            $status_approved = $request->status_approved;
            $id_izinsakit_form = $request->id_izinsakit_form;

            $data = ['status_approved' => $status_approved];

            if($status_approved == 2 || $status_approved == 3){
                $data['catatan_penolakan'] = $request->catatan_penolakan;
            } else {
                $data['catatan_penolakan'] = null;
            }

            $update = DB::table('pengajuan_izin')->where('id', $id_izinsakit_form)->update($data);

            if($update){
                return Redirect::back()->with(['success' => 'Data berhasil diupdate']);
            }else{
                return Redirect::back()->with(['warning' => 'Data gagal diupdate']);
            }
        }

          public function batalkanizinsakit($id)
        {

            $update = DB::table('pengajuan_izin')->where('id', $id)->update([
                'status_approved' => 0
            ]);
            if($update){
                return Redirect::back()->with(['success' => 'Data berhasil diupdate']);
            }else{
                return Redirect::back()->with(['warning' => 'Data gagal diupdate']);
            }
        }

        public function hapusizinsakit($id)
        {
            $delete = DB::table('pengajuan_izin')->where('id', $id)->delete();

            if ($delete) {
                return Redirect::back()->with(['success' => 'Data berhasil dihapus']);
            } else {
                return Redirect::back()->with(['warning' => 'Data gagal dihapus']);
            }
        }



}
