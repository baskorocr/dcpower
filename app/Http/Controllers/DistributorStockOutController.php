<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductTraceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributorStockOutController extends Controller
{
    public function index()
    {
        return view('distributors.stock-out');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string'
        ]);

        $product = Product::where('serial_number', $request->serial_number)
            ->where('at_distributor', '>', 0)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan atau stok di distributor kosong'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'serial_number' => $product->serial_number,
                'project_name' => $product->project->name ?? '-',
                'at_distributor' => $product->at_distributor
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::where('serial_number', $request->serial_number)
                ->lockForUpdate()
                ->first();

            if (!$product || $product->at_distributor < $request->quantity) {
                throw new \Exception('Stok tidak mencukupi');
            }

            $product->at_distributor -= $request->quantity;
            $product->at_retail += $request->quantity;
            $product->save();

            ProductTraceLog::create([
                'product_id' => $product->id,
                'event_type' => 'stock_out_to_retail',
                'quantity' => $request->quantity,
                'performed_by' => auth()->id(),
                'notes' => 'Stock out dari distributor ke retail'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'product' => [
                    'serial_number' => $product->serial_number,
                    'at_distributor' => $product->at_distributor,
                    'at_retail' => $product->at_retail
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
