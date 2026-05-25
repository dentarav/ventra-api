<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Asset;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function borrow(Request $request)
    {
    $request->validate(['asset_id' => 'required|exists:assets,id']);
    
    $asset = Asset::find($request->asset_id);
    
    if ($asset->status !== 'available') {
        return response()->json(['message' => 'Barang sedang dipakai/tidak tersedia!'], 400);
    }

    $transaction = Transaction::create([
        'user_id' => auth()->user()->id,
        'asset_id' => $request->asset_id,
        'status' => 'borrowed',
        'borrowed_at' => now(),
    ]);

    $asset->update(['status' => 'In Use']);

    return response()->json(['message' => 'Berhasil meminjam barang', 'data' => $transaction], 201);
    }
}
