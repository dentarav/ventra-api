<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $assets = $query->paginate(5);

        return response()->json([
            'success' => true,
            'message' => 'Daftar Aset Ventra',
            'data' => $assets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category' => 'required|string',
            'condition' => 'required|string'
        ]);

        $asset = Asset::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil ditambahkan',
            'data' => $asset
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Aset tidak ditemukan'], 404);
        }

        $asset->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data aset berhasil diperbarui',
            'data' => $asset
        ]);
    }

    public function destroy($id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Aset tidak ditemukan'], 404);
        }

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil dihapus dari sistem'
        ]);
    }
}
