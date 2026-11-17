<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('records the authenticated user as the movement owner', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $product = Product::query()->create([
        'name' => 'Caja de tornillos',
        'sku' => 'TOR-001',
        'category' => 'Ferretería',
    ]);

    $this->actingAs($user)
        ->post(route('movements.store'), [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 5,
            'occurred_on' => today()->toDateString(),
            'supplier' => 'Proveedor S.A.',
        ])
        ->assertRedirect(route('stock.index'));

    $this->assertDatabaseHas('movements', [
        'product_id' => $product->id,
        'user_id' => $user->id,
        'type' => 'in',
        'quantity' => 5,
    ]);
});

it('rejects an outbound reason longer than its database column', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $product = Product::query()->create([
        'name' => 'Caja de tornillos',
        'sku' => 'TOR-002',
        'category' => 'Ferretería',
    ]);

    $this->from(route('stock.index'))
        ->actingAs($user)
        ->post(route('movements.store'), [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 1,
            'occurred_on' => today()->toDateString(),
            'reason' => str_repeat('a', 101),
        ])
        ->assertRedirect(route('stock.index'))
        ->assertSessionHasErrors('reason');

    $this->assertDatabaseMissing('movements', [
        'product_id' => $product->id,
    ]);
});

it('conserva el saldo y el historial si una salida excede el stock', function(){
    $operador = User::factory()->create(['role' => 'operator']);
    $product = Product::query()->create([
        'name' => 'Cable USB',
        'category' => 'Insumos',
        'sku' => 'DEMO-48'
    ]);

    $base = [
        'product_id' => $product->id,
        'occurred_on' => today()->toDateString(),
    ];

    $this->actingAs($operador)->post(route('movements.store'), $base + [
        'type' => 'in',
        'quantity' => 10,
        'supplier' => 'Proveedor demo',
    ])->assertRedirectToRoute('stock.index')->assertSessionHasNoErrors();

    $this->post(route('movements.store'), $base + [
        'type' => 'out',
        'quantity' => 4,
        'reason' => 'Pedido',
    ])->assertRedirectToRoute('stock.index')->assertSessionHasNoErrors();

    $this->from(route('stock.index'))->post(route('movements.store'), $base + [
        'type' => 'out',
        'quantity' => 7,
        'reason' => 'Pedido',
    ])->assertRedirectToRoute('stock.index')->assertSessionHasErrors('quantity');

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 6]);
    $this->assertDatabaseCount('movements', 2);
});
