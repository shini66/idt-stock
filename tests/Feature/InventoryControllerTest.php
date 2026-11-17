<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('devuelve id y stock al operario autenticado', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $product = Product::query()->create([
        'name' => 'Cable',
        'sku' => 'JSON-48',
        'category' => 'Insumos',
    ]);

    $this->actingAs($operator)
        ->getJson(route('stock.live'))
        ->assertOk()
        ->assertExactJson([['id' => $product->id, 'stock' => 0]]);
});
