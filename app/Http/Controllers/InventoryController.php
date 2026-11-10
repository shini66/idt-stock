<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', 'in:in,out'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $catalog = Product::query();

        if($search = $filters['q'] ?? null){
            $catalog->where(function ($query) use ($search) {
                $query->where('sku','like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if($category = $filters['category'] ?? null) {
            $catalog->where('category', $category);
        }

        $history = Movement::with('product')->whereHas('product', function ($query) use ($catalog) {
            $query->whereIn('products.id', (clone $catalog)->withTrashed()->select('products.id'));
        });

        if ($type = $filters['type'] ?? null) {
            $history->where('type', $type);
        }
        if ($from = $filters['from'] ?? null) {
            $history->where('occurred_on', '>=', $from);
        }
        if ($to = $filters['to'] ?? null) {
            $history->where('occurred_on', '<=', $to);
        }

        return view('inventory.index', [
            'products' => $catalog->orderBy('name')->paginate(12, ['*'], 'catalog_page')->withQueryString(),
            'movements' => $history->latest('occurred_on')->latest('id')->paginate(15, ['*'], 'history_page')->withQueryString(),
            'options' => Product::orderBy('name')->get(['id', 'sku', 'name']),
            'totals' => Product::query()->selectRaw('category, SUM(stock) as units')
                ->groupBy('category')->orderBy('category')->get(),
        ]);
    }

    public function live(){
        return response()->json(Product::orderBy('id')->get(['id', 'stock']))
            ->header('Cache-Control', 'private, no-store');
    }
}
