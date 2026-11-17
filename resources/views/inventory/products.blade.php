<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Catálogo de productos</h2>
                <p class="mt-1 text-sm text-gray-500">Buscá, consultá y administrá los productos disponibles.</p>
            </div>

            @can('manage-product')
                <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Nuevo producto</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200" aria-labelledby="filters-heading">
                <h3 id="filters-heading" class="text-lg font-semibold text-gray-900">Filtrar productos</h3>
                <form method="GET" action="{{ route('stock.products') }}" class="mt-4 grid gap-4 md:grid-cols-3">
                    <div>
                        <x-label for="q" value="Buscar producto" />
                        <x-input id="q" name="q" type="search" value="{{ request('q') }}" class="mt-1 block w-full" placeholder="SKU o nombre" />
                    </div>
                    <div>
                        <x-label for="category" value="Categoría" />
                        <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <x-button>Aplicar filtros</x-button>
                        <a href="{{ route('stock.products') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Limpiar</a>
                    </div>
                </form>
            </section>

            <section class="min-w-0 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200" aria-labelledby="catalog-heading">
                <div class="flex items-center justify-between gap-4">
                    <h3 id="catalog-heading" class="text-lg font-semibold text-gray-900">Productos</h3>
                    <span class="text-sm text-gray-500">{{ $products->total() }} productos</span>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                        <thead class="text-xs uppercase tracking-wider text-gray-500">
                            <tr>
                                <th scope="col" class="px-3 py-3">Producto</th>
                                <th scope="col" class="px-3 py-3">SKU</th>
                                <th scope="col" class="px-3 py-3">Categoría</th>
                                <th scope="col" class="px-3 py-3 text-right">Stock</th>
                                @can('manage-product')
                                    <th scope="col" class="px-3 py-3"><span class="sr-only">Acciones</span></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-3 py-4 font-medium text-gray-900">
                                        {{ $product->name }}
                                        @if ($product->description)
                                            <p class="mt-1 max-w-xs text-xs font-normal text-gray-500">{{ $product->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4">{{ $product->sku }}</td>
                                    <td class="px-3 py-4">{{ $product->category }}</td>
                                    <td class="px-3 py-4 text-right font-semibold">{{ number_format($product->stock) }}</td>
                                    @can('manage-product')
                                        <td class="px-3 py-4 text-right"><a href="{{ route('products.edit', $product) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Editar</a></td>
                                    @endcan
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No se encontraron productos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">{{ $products->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
