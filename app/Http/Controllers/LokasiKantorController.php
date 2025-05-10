<?php

namespace App\Http\Controllers;

use App\Models\LokasiKantor;
use Illuminate\Http\Request;

class LokasiKantorController extends Controller
{
    public function index()
    {
        $title = "Lokasi Kantor";
        $lokasiKantor = LokasiKantor::paginate(10);
        return view('admin.lokasi-kantor.index', compact('title', 'lokasiKantor'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kota' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|numeric',
            'is_used' => 'required|boolean',
        ]);

        // Tidak perlu pengecekan khusus, semua lokasi bisa aktif
        $create = LokasiKantor::create($data);

        if ($create) {
            return to_route('admin.lokasi-kantor')->with('success', 'Data Lokasi Kantor berhasil disimpan');
        } else {
            return to_route('admin.lokasi-kantor')->with('error', 'Data Lokasi Kantor gagal disimpan');
        }
    }

    public function edit(Request $request)
    {
        $data = LokasiKantor::where('id', $request->id)->first();
        return $data;
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'kota' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|numeric',
            'is_used' => 'required|boolean',
        ]);

        // Tidak perlu menonaktifkan lokasi lain, karena semua lokasi bisa aktif
        $update = LokasiKantor::where('id', $request->id)->update($data);

        if ($update) {
            return to_route('admin.lokasi-kantor')->with('success', 'Data Lokasi Kantor berhasil diperbarui');
        } else {
            return to_route('admin.lokasi-kantor')->with('error', 'Data Lokasi Kantor gagal diperbarui');
        }
    }

    public function toggleStatus(Request $request)
    {
        $lokasiKantor = LokasiKantor::findOrFail($request->id);
        $lokasiKantor->is_used = !$lokasiKantor->is_used;
        $lokasiKantor->save();

        return response()->json([
            'success' => true,
            'message' => 'Status lokasi kantor berhasil diubah',
            'is_used' => $lokasiKantor->is_used
        ]);
    }

    public function delete(Request $request)
    {
        if (LokasiKantor::count() == 1) {
            return response()->json(['success' => false, 'message' => 'Data Lokasi Kantor Gagal dihapus. Minimal harus ada satu lokasi kantor.']);
        }

        $delete = LokasiKantor::where('id', $request->id)->delete();

        if ($delete) {
            return response()->json(['success' => true, 'message' => 'Data Lokasi Kantor Berhasil dihapus']);
        } else {
            return response()->json(['success' => false, 'message' => 'Data Lokasi Kantor Gagal dihapus']);
        }
    }
}
