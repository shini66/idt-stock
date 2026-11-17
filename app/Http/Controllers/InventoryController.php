<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('inventory.dashboard', [
            'productCount' => Product::query()->count(),
            'stockUnits' => Product::query()->sum('stock'),
            'todayMovements' => Movement::query()->whereDate('occurred_on', today())->count(),
            'totals' => Product::query()
                ->selectRaw('category, SUM(stock) as units')
                ->groupBy('category')
                ->orderBy('category')
                ->get(),
            'recentMovements' => Movement::query()
                ->with('product')
                ->latest('occurred_on')
                ->latest('id')
                ->limit(6)
                ->get(),
        ]);
    }

    public function products(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
        ]);

        $catalog = Product::query();

        if ($search = $filters['q'] ?? null) {
            $catalog->where(function ($query) use ($search) {
                $query->where('sku', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($category = $filters['category'] ?? null) {
            $catalog->where('category', $category);
        }

        return view('inventory.products', [
            'products' => $catalog->orderBy('name')->paginate(12)->withQueryString(),
            'categories' => Product::query()->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function movements(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:80'],
            'type' => ['nullable', 'in:in,out'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $history = Movement::query()->with('product');

        if ($search = $filters['q'] ?? null) {
            $history->whereHas('product', function ($query) use ($search) {
                $query->where('sku', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($type = $filters['type'] ?? null) {
            $history->where('type', $type);
        }

        if ($from = $filters['from'] ?? null) {
            $history->where('occurred_on', '>=', $from);
        }

        if ($to = $filters['to'] ?? null) {
            $history->where('occurred_on', '<=', $to);
        }

        return view('inventory.movements', [
            'movements' => $history->latest('occurred_on')->latest('id')->paginate(15)->withQueryString(),
            'options' => Product::query()->orderBy('name')->get(['id', 'sku', 'name']),
        ]);
    }

    public function live(): JsonResponse
    {
        return response()->json(Product::query()->orderBy('id')->get(['id', 'stock']))
            ->header('Cache-Control', 'private, no-store');
    }
}
