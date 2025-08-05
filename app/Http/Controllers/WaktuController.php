<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waktu;
use App\Imports\WaktuImport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class WaktuController extends Controller
{
    public function index()
    {
        $waktu = Waktu::orderBy('jam_ke')->paginate(6);
        return view('waktu.index', compact('waktu'));
    }

    public function create()
    {
        return view('waktu.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required|string|max:100',
            'jam_ke' => 'required|string|max:100',
            'jam_mulai' => 'required|string|max:100',
            'jam_selesai' => 'nullable|string|max:100',
            'ket' => 'nullable|string|max:50',
            
        ]);

        Waktu::create($validated);
        return redirect()->route('waktu.index')->with('success', 'Waktu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $waktu = Waktu::findOrFail($id);
        return view('waktu.edit', compact('waktu'));
    }

    public function update(Request $request, $id)
    {
        $waktu = Waktu::findOrFail($id);

        $validated = $request->validate([
            'hari' => 'required|string|max:100',
            'jam_ke' => 'required|string|max:100',
            'jam_mulai' => 'required|string|max:100',
            'jam_selesai' => 'nullable|string|max:100',
            'ket' => 'nullable|string|max:50',
        ]);

        $waktu->update($validated);
        return redirect()->route('waktu.index')->with('success', 'Waktu berhasil diperbarui.');
    }

    public function delete($id)
    {
        $waktu = Waktu::findOrFail($id);
        $waktu->delete();

        return redirect()->route('waktu.index')->with('success', 'Waktu berhasil dihapus.');
    }

    public function reset()
    {
        try {
            \App\Models\Waktu::query()->delete();
            return redirect()->route('waktu.index')->with('success', 'Seluruh data waktu berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new WaktuImport, $request->file('file'));

    return redirect()->route('waktu.index')->with('success', 'Data waktu berhasil diimpor.');
    }
}
