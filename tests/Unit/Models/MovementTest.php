<?php

use App\Models\Movement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

uses(TestCase::class);

it('defines typed singular product and user relationships', function () {
    $movement = new Movement;

    expect($movement->product())->toBeInstanceOf(BelongsTo::class);
    expect($movement->product()->getRelated())->toBeInstanceOf(Product::class);
    expect($movement->user())->toBeInstanceOf(BelongsTo::class);
    expect($movement->user()->getRelated())->toBeInstanceOf(User::class);
});
