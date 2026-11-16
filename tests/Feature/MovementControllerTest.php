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
