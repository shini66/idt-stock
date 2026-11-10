<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function create()
    {
        return view('invetory.product-from', ['product' => new Product]);
    }

    public function store(Request $request)
    {
        Product::create($this->data($request));
        return to_route('stock.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $request)
    {
        return view('inventory.product-form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->data($request, $product));
        return to_route('stock.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $current = Product::query()->lockForUpdate()->findOrFail($product->id);
            if ($current->stock !== 0) {
                throw ValidationException::withMessages([
                    'product' => 'El producto debe tener stock cero para archivarse.',
                ]);
            }
            $current->delete();
        });

        return to_route('stock.index')->with('success', 'Producto archivado.');
    }


    private function data(Request $request, ?Product $product = null): array
    {
        $uniqueSku = Rule::unique('product', 'sku');
        if($product !== null){
            $uniqueSku->ignore($product);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sku' => ['required', 'string', 'max:40', $uniqueSku],
            'category' => ['required', 'string', 'max:80'],
        ]);
    }
}
