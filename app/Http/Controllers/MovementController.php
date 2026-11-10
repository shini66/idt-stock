<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovementController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99999'],
            'occurred_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'supplier' => ['exclude_unless:type,in', 'required', 'string', 'max:120'],
            'reason' => ['exclude_unless:type,out', 'required', 'string', 'max:180'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);
            $quantity = (int) $data['quantity'];
            $next = $data['type'] === 'in' ? $product->stock + $quantity : $product->stock - $quantity;

            if($next < 0 || $next > 99999) {
                throw ValidationException::withMessages([
                    'quantity' => $next < 0 ? 'Stock Insuficiente' : 'Se supero el limite de stock'
                ]);
            }

            $product->stock = $next;
            $product->save();

            $product->movements()->create([
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'occurred_on' => $data['occurred_on'],
                'supplier' => $data['supplier'],
                'reason' => $data['reason'],
                'user_id' => $data['user_id'],
            ]);
        }, 3);

        return to_route('stock.index')->with('success', 'Movimiento registrado');
    }
}
