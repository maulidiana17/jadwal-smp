<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Imports\PengampuImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class PengampuController extends Controller
{
    // public function index()
    // {
    //     $data = Pengampu::with(['guru', 'mapel','kelas'])->get();
    //     return view('pengampu.index', compact('data'));
    // }
    public function index(Request $request)
    {
        $raw = \App\Models\Pengampu::with(['guru', 'mapel', 'kelas'])
            ->orderBy('guru_id')
            ->orderBy('mapel_id')
            ->orderBy('kelas_id')
            ->get()
            ->groupBy(fn($item) => $item->guru_id . '-' . $item->mapel_id)
            ->values();

        $perPage = 10;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $currentItems = $raw->slice($offset, $perPage);
        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $raw->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pengampu.index', ['groups' => $paginated]);
    }


    public function create()
    {
        return view('pengampu.create', [
            'guruList' => Guru::all(),
            'mapelList' => Mapel::all(),
            'kelasList' => Kelas::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'exists:kelas,id'
        ]);

        foreach ($request->kelas_ids as $kelas_id) {
            \App\Models\Pengampu::firstOrCreate([
                'guru_id' => $request->guru_id,
                'mapel_id' => $request->mapel_id,
                'kelas_id' => $kelas_id,
            ]);
        }

        return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil disimpan.');
    }

    public function view()
    {
        $groups = \App\Models\Pengampu::with(['guru', 'mapel', 'kelas'])
            ->get()
            ->groupBy(fn($item) => $item->guru_id . '-' . $item->mapel_id);

        return view('pengampu.index', ['groups' => $groups]);
    }

    // public function editMultiple($guru_id, $mapel_id)
    // {
    //     $kelasList = \App\Models\Kelas::all();
    //     $pengampuGroup = \App\Models\Pengampu::with('guru', 'mapel')
    //         ->where('guru_id', $guru_id)
    //         ->where('mapel_id', $mapel_id)
    //         ->first();

    //     $kelasSelected = \App\Models\Pengampu::where('guru_id', $guru_id)
    //         ->where('mapel_id', $mapel_id)
    //         ->pluck('kelas_id')
    //         ->toArray();

    //     return view('pengampu.edit', compact('pengampuGroup', 'kelasList', 'kelasSelected'));
    // }
    public function editMultiple($guru_id, $mapel_id)
    {
        $pengampuGroup = Pengampu::where('guru_id', $guru_id)
            ->where('mapel_id', $mapel_id)
            ->firstOrFail();

        $kelasSelected = Pengampu::where('guru_id', $guru_id)
            ->where('mapel_id', $mapel_id)
            ->pluck('kelas_id')
            ->toArray();

        $kelasList = Kelas::orderBy('nama')->get();
        $mapelList = Mapel::orderBy('mapel')->get(); // <--- penting

        return view('pengampu.edit', compact('pengampuGroup', 'kelasList', 'kelasSelected', 'mapelList'));
    }
    
    public function updateMultiple(Request $request, $guru_id, $old_mapel_id)
    {
        $request->validate([
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_ids' => 'required|array|min:1',
        ]);

        $new_mapel_id = $request->input('mapel_id');
        $kelas_ids = $request->input('kelas_ids');

        // 1. Hapus semua pengampu lama untuk guru + mapel lama
        Pengampu::where('guru_id', $guru_id)
            ->where('mapel_id', $old_mapel_id)
            ->delete();

        // 2. Tambahkan pengampu baru untuk guru + mapel baru ke kelas-kelas terpilih
        foreach ($kelas_ids as $kelas_id) {
            Pengampu::create([
                'guru_id' => $guru_id,
                'mapel_id' => $new_mapel_id,
                'kelas_id' => $kelas_id,
            ]);
        }

        return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil diperbarui.');
    }


    // public function updateMultiple(Request $request, $guru_id, $mapel_id)
    // {
    //     $request->validate([
    //         'kelas_ids' => 'required|array|min:1',
    //         'kelas_ids.*' => 'exists:kelas,id',
    //     ]);

    //     \App\Models\Pengampu::where('guru_id', $guru_id)
    //         ->where('mapel_id', $mapel_id)
    //         ->delete();

    //     foreach ($request->kelas_ids as $kelas_id) {
    //         \App\Models\Pengampu::create([
    //             'guru_id' => $guru_id,
    //             'mapel_id' => $mapel_id,
    //             'kelas_id' => $kelas_id,
    //         ]);
    //     }

    //     return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil diperbarui.');
    // }


    public function destroyGroup($guru_id, $mapel_id)
    {
        \App\Models\Pengampu::where('guru_id', $guru_id)
            ->where('mapel_id', $mapel_id)
            ->delete();

        return back()->with('success', 'Semua kelas pengampu untuk guru & mapel ini telah dihapus.');
    }
    // public function create()
    // {
    //     $guru = Guru::all();
    //     $mapel = Mapel::all();
    //     $kelas = Kelas::all();
    //     return view('pengampu.create', compact('guru', 'mapel','kelas'));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'guru_id' => 'required|exists:guru,id',
    //         'mapel_id' => 'required|exists:mapel,id',
    //         'kelas_id' => 'required|exists:kelas,id',
    //     ]);

    //     Pengampu::create($request->all());
    //     return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil disimpan.');
    // }

    // public function edit($id)
    // {
    //     $pengampu = Pengampu::findOrFail($id);
    //     $guru = Guru::all();
    //     $mapel = Mapel::all();
    //     $kelas = Kelas::all();
    //     return view('pengampu.edit', compact('pengampu', 'guru', 'mapel','kelas'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'guru_id' => 'required|exists:guru,id',
    //         'mapel_id' => 'required|exists:mapel,id',
    //         'kelas_id' => 'required|exists:kelas,id',
    //     ]);

    //     $pengampu = Pengampu::findOrFail($id);
    //     $pengampu->update($request->all());

    //     return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil diperbarui.');
    // }

    // public function destroy($id)
    // {
    //     $pengampu = Pengampu::findOrFail($id);
    //     $pengampu->delete();

    //     return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil dihapus.');
    // }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new PengampuImport, $request->file('file'));

    return redirect()->route('pengampu.index')->with('success', 'Data pengampu berhasil diimpor.');
    }
}