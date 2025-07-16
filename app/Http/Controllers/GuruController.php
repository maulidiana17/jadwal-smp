<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Imports\GuruImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::orderBy('nama')->paginate(5);
        return view('guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_guru' => 'required|unique:guru,kode_guru|max:10',
            'nama' => 'required|string|max:100',
            'nip' => 'required|unique:guru,nip',
            'email' => 'required|email|unique:guru,email',
            'alamat' => 'nullable|string|max:255',
        ]);

        Guru::create($validated);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'kode_guru' => 'required|max:10|unique:guru,kode_guru,' . $guru->id,
            'nama' => 'required|string|max:100',
            'nip' => 'required|unique:guru,nip,' . $guru->id,
            'email' => 'required|email|unique:guru,email,' . $guru->id,
            'alamat' => 'nullable|string|max:255',
        ]);

        $guru->update($validated);
        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function delete($id)
    {
        $guru = Guru::findOrFail($id);
        $guru->delete();

        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus.');
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
