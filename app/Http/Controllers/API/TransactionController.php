<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Helpers\ResponseFormatter;
use App\Models\Product;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $status = $request->input('status');

        if ($id) {
            $transaction = Transaction::with(['products', 'user'])->find($id);

            if ($transaction) {
                return ResponseFormatter::success($transaction, 'Data transaksi berhasil diload!');
            } else {
                return ResponseFormatter::error(null, 'Data transaksi tidak ada!', 404);
            }
        }

        $transactions = Transaction::with(['user', 'products'])->where('user_id', $request->user()->id);

        if ($status) {
            $transactions->where('status', $status);
        }

        return ResponseFormatter::success($transactions->paginate($limit), 'List transaksi berhasil di load!');
    }

    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'exists:products,id',
                'items.*.qty' => 'required',
                'items.*.total' => 'required',
                'ongkir' => 'required',
                'grandTotal' => 'required',
                'address' => 'required|string',
                'status' => 'in:ONPROCESS,DELIVERED,FAILED,PENDING'
            ]);

            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'ongkir' => $request->input('ongkir'),
                'grandTotal' => $request->input('grandTotal'),
                'address' => $request->input('address'),
                'paymentMethod' => $request->input('paymentMethod'),
                'status' => $request->input('status'),
            ]);

            foreach ($request->items as $items) {
                $transaction->products()->attach($transaction->id, [
                    'product_id' => $items['product_id'],
                    'productName' => Product::where('id', $items['product_id'])->pluck('productName')->first(),
                    'qty' => $items['qty'],
                    'total' => $items['total']
                ]);
            }

            return ResponseFormatter::success($transaction->load('products'), 'Checkout berhasil!');

        } catch (Exception $error) {
            return ResponseFormatter::error(null, 'Checkout gagal!', 500);
        }
    }

}
