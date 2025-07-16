<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;
use App\Imports\MapelImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class MapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::orderBy('mapel')->paginate(5);
        return view('mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('mapel.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_mapel' => 'required|unique:mapel,kode_mapel|max:10',
            'mapel' => 'required|string|max:100',
            'jam_per_minggu' => 'required|string|max:20',
            'ruang_khusus' => 'nullable|string|max:255',
        ]);

        Mapel::create($validated);
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mapel = Mapel::findOrFail($id);
        return view('mapel.edit', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $validated = $request->validate([
            'kode_mapel' => 'required|max:10|unique:mapel,kode_mapel,' . $mapel->id,
            'mapel' => 'required|string|max:100',
            'jam_per_minggu' => 'required|string|max:20',
            'ruang_khusus' => 'nullable|string|max:255',
        ]);

        $mapel->update($validated);
        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil diperbarui.');
    }

    public function delete($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->route('mapel.index')->with('success', 'Mapel berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new MapelImport, $request->file('file'));

    return redirect()->route('mapel.index')->with('success', 'Data mapel berhasil diimpor.');
    }
}
