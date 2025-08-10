<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Imports\RuanganImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;


class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::orderBy('nama')->paginate(5);
        return view('ruangan.index', compact('ruangans'));
    }

    public function create()
    {
        return view('ruangan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan|max:10',
            'nama' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:255',
            'fasilitas' => 'nullable|string|max:255',
        ]);

        Ruangan::create($validated);
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $validated = $request->validate([
            'kode_ruangan' => 'required|max:10|unique:ruangan,kode_ruangan,' . $ruangan->id,
            'nama' => 'required|string|max:100',
            'tipe' => 'nullable|string|max:255',
            'fasilitas' => 'nullable|string|max:255',
        ]);

        $ruangan->update($validated);
        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    public function reset()
    {
        try {
            \App\Models\Ruangan::query()->delete();
            // Reset auto increment ke 1
            \DB::statement('ALTER TABLE ruangan AUTO_INCREMENT = 1');
            return redirect()->route('ruangan.index')->with('success', 'Seluruh data ruangan berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new RuanganImport, $request->file('file'));

    return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diimpor.');
    }
}
