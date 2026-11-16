<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a product when its sku is unique', function () {
    $user = User::factory()->create();
    $user->role = 'admin';
    $user->save();

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Caja de tornillos',
            'description' => 'Tornillos galvanizados',
            'sku' => 'TOR-001',
            'category' => 'Ferretería',
        ])
        ->assertRedirect(route('stock.index'));

    $this->assertDatabaseHas('products', [
        'name' => 'Caja de tornillos',
        'sku' => 'TOR-001',
    ]);
});

it('allows a product to retain its sku when updated', function () {
    $user = User::factory()->create();
    $user->role = 'admin';
    $user->save();
    $product = Product::query()->create([
        'name' => 'Caja de tornillos',
        'sku' => 'TOR-001',
        'category' => 'Ferretería',
    ]);

    $this->actingAs($user)
        ->put(route('products.update', $product), [
            'name' => 'Caja de tornillos premium',
            'description' => 'Tornillos galvanizados',
            'sku' => 'TOR-001',
            'category' => 'Ferretería',
        ])
        ->assertRedirect(route('stock.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Caja de tornillos premium',
        'sku' => 'TOR-001',
    ]);
});

it('rejects an update when another product already uses the sku', function () {
    $user = User::factory()->create();
    $user->role = 'admin';
    $user->save();
    $product = Product::query()->create([
        'name' => 'Caja de tornillos',
        'sku' => 'TOR-001',
        'category' => 'Ferretería',
    ]);
    $productWithSku = Product::query()->create([
        'name' => 'Tuercas',
        'sku' => 'TUE-001',
        'category' => 'Ferretería',
    ]);

    $this->actingAs($user)
        ->put(route('products.update', $product), [
            'name' => 'Caja de tornillos premium',
            'description' => 'Tornillos galvanizados',
            'sku' => $productWithSku->sku,
            'category' => 'Ferretería',
        ])
        ->assertSessionHasErrors('sku');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Caja de tornillos',
        'sku' => 'TOR-001',
    ]);
});
