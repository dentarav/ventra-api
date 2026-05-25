<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Asset::with(['category', 'location']);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }

        $sortBy = $request->get('sort_by', 'id');
        $order = $request->get('order', 'desc');
        $query->orderBy($sortBy, $order);

        return response()->json([
            'success' => true,
            'data' => $query->paginate(5)
        ], 200);
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'location_id' => 'required|exists:locations,id',
        'condition' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' 
    ]);

    $data = $request->all();

    if ($request->hasFile('image')) {
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);
        $data['image'] = 'images/'.$imageName;
    }

    $data['user_id'] = auth()->user()->id;

    $asset = Asset::create($data);

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

    public function report()
    {
        $totalAssets = Asset::count();
        $kondisiBagus = Asset::where('condition', 'Good')->count();
        $kondisiRusak = Asset::where('condition', 'Broken')->count();
        
        $perCategory = Asset::select('category_id', \DB::raw('count(*) as total'))
                            ->with('category') 
                            ->groupBy('category_id')
                            ->get();

        return response()->json([
            'report_date' => now()->toFormattedDateString(),
            'summary' => [
                'total_barang' => $totalAssets,
                'kondisi_bagus' => $kondisiBagus,
                'kondisi_rusak' => $kondisiRusak,
            ],
            'kategori_breakdown' => $perCategory
        ], 200);
    }
}
