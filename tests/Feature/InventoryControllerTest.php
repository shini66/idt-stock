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

it('renders the inventory overview and recent movements for an operator', function () {
    $operator = User::factory()->create(['role' => 'operator']);

    $this->actingAs($operator)
        ->get(route('stock.index'))
        ->assertOk()
        ->assertSee('Resumen del inventario')
        ->assertSee('Movimientos recientes');
});

it('renders the focused catalog screen for an operator', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $product = Product::query()->create([
        'name' => 'Cable de red',
        'sku' => 'CAT-001',
        'category' => 'Insumos',
    ]);

    $this->actingAs($operator)
        ->get(route('stock.products'))
        ->assertOk()
        ->assertSee('Catálogo de productos')
        ->assertSee($product->name);
});

it('renders the focused movements screen and registration form for an operator', function () {
    $operator = User::factory()->create(['role' => 'operator']);
    $product = Product::query()->create([
        'name' => 'Cable de red',
        'sku' => 'MOV-001',
        'category' => 'Insumos',
    ]);

    $this->actingAs($operator)
        ->get(route('stock.movements'))
        ->assertOk()
        ->assertSee('Historial de movimientos')
        ->assertSee('Registrar movimiento')
        ->assertSee($product->name);
});
