@extends('layout.main')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Jadwal Pelajaran</h3>
        <a href="{{ route('jadwal.generate') }}" class="btn btn-success">
            <i class="fa fa-cogs"></i> Generate Jadwal
        </a>
    </div>

    @if($jadwals->isEmpty())
        <div class="alert alert-warning">
            Belum ada jadwal yang dihasilkan. Silakan klik tombol "Generate Jadwal".
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Kelas</th>
                        <th>Hari</th>
                        <th>Jam Ke</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Ruangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwals as $jadwal)
                        <tr>
                            <td>{{ $jadwal->kelas->nama }}</td>
                            <td>{{ $jadwal->hari }}</td>
                            <td>{{ $jadwal->jam_ke }}</td>
                            <td>{{ $jadwal->mapel->nama ?? '-' }}</td>
                            <td>{{ $jadwal->guru->nama ?? '-' }}</td>
                            <td>{{ $jadwal->ruangan->nama ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

do {
    $hari = rand(1, 6);
    $jam_ke = rand(1, 6);
} while (in_array($jam_ke, $this->excludedTimes[$hari] ?? []));

$ruangan = $this->getRequiredRoom($mapel);

{{-- cromosom --}}

$chromosome[] = [
    'kelas_id' => $kelas->id,
    'mapel_id' => $mapel->id,
    'guru_id' => $guru->id,
    'hari' => $hari,
    'jam_ke' => $jam_ke,
    'ruangan_id' => $ruangan?->id,
];

composer dump-autoload


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Imports\GuruImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class GuruController extends Controller
{
    public function index()
    {
        try {
            $guru = Guru::all();
            return view('guru.index', compact('guru'));
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data guru: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data guru.');
        }
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_guru' => 'required|unique:guru,kode_guru|max:10',
            'nama' => 'required|string|max:255',
            'nip' => 'required|numeric|unique:guru,nip',
            'email' => 'required|email|unique:guru,email',
            'alamat' => 'required|string|max:255'
        ]);

        try {
            Guru::create([
                'kode_guru' => $request->kode_guru,
                'nama' => $request->nama,
                'nip' => $request->nip,
                'email' => $request->email,
                'alamat' => $request->alamat
            ]);

            return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data guru: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan data guru.')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $guru = Guru::findOrFail($id);
            return view('guru.edit', compact('guru'));
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data guru untuk edit: ' . $e->getMessage());
            return back()->with('error', 'Data guru tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_guru' => 'required|unique:guru,kode_guru|max:10' .$id,
            'nama' => 'required|string|max:255',
            'nip' => 'required|numeric|unique:guru,nip,' .$id,
            'email' => 'required|email|unique:guru,email' .$id,
            'alamat' => 'nullable|string|max:255'
        ]);

        try {
            $guru = Guru::findOrFail($id);
            $guru->update([
                'kode_guru' => $request->kode_guru,
                'nama' => $request->nama,
                'nip' => $request->nip,
                'email' => $request->email,
                'alamat' => $request->alamat
            ]);

            return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui data guru: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data guru.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $guru = Guru::findOrFail($id);
            $guru->delete();

            return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus data guru: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data guru.');
        }
    }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new GuruImport, $request->file('file'));

    return redirect()->route('guru.index')->with('success', 'Data guru berhasil diimpor.');
    }
}
