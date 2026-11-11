<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $product->exists ? 'Editar producto' : 'Nuevo producto' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">Los movimientos son los únicos que modifican el stock.</p>
            </div>

            <a href="{{ route('stock.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Volver al inventario
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <x-validation-errors class="mb-6 rounded-md border border-red-200 bg-red-50 p-4" />

                <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" class="flex flex-col gap-6">
                    @csrf
                    @if ($product->exists)
                        @method('PUT')
                    @endif

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-label for="name" value="Nombre" />
                            <x-input id="name" name="name" type="text" maxlength="120" value="{{ old('name', $product->name) }}" required autofocus class="mt-1 block w-full" />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="sku" value="SKU" />
                            <x-input id="sku" name="sku" type="text" maxlength="40" value="{{ old('sku', $product->sku) }}" required class="mt-1 block w-full" />
                            <x-input-error for="sku" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="category" value="Categoría" />
                            <x-input id="category" name="category" type="text" maxlength="80" value="{{ old('category', $product->category) }}" required class="mt-1 block w-full" />
                            <x-input-error for="category" class="mt-2" />
                        </div>

                        @if ($product->exists)
                            <div>
                                <x-label for="stock" value="Stock actual" />
                                <x-input id="stock" type="text" value="{{ number_format($product->stock) }}" disabled class="mt-1 block w-full bg-gray-50" />
                                <p class="mt-2 text-sm text-gray-500">Registrá una entrada o salida para modificarlo.</p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <x-label for="description" value="Descripción" />
                        <textarea id="description" name="description" rows="5" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stock.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</a>
                        <x-button>{{ $product->exists ? 'Guardar cambios' : 'Crear producto' }}</x-button>
                    </div>
                </form>

                @if ($product->exists)
                    @can('manage-product')
                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Archivar este producto?');" class="mt-6 border-t border-gray-200 pt-6">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>Archivar producto</x-danger-button>
                        </form>
                    @endcan
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
