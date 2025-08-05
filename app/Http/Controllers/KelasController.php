<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Imports\KelasImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::orderBy('nama')->paginate(5);
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('kelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tingkat_kelas' => 'nullable|string|max:255',
        ]);

        Kelas::create($validated);
        return redirect()->route('kelas.index')->with('success', 'kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'tingkat_kelas' => 'nullable|string|max:255',
        ]);

        $kelas->update($validated);
        return redirect()->route('kelas.index')->with('success', 'kelas berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'kelas berhasil dihapus.');
    }

    public function reset()
    {
        try {
            \App\Models\Kelas::query()->delete();
            return redirect()->route('kelas.index')->with('success', 'Seluruh data kelas berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new KelasImport, $request->file('file'));

    return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diimpor.');
    }
}
