<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard de inventario</h2>
            <p class="mt-1 text-sm text-gray-500">Un resumen del stock y de la actividad más reciente.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <section aria-labelledby="summary-heading">
                <h3 id="summary-heading" class="text-lg font-semibold text-gray-900">Resumen del inventario</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm font-medium text-gray-500">Productos activos</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($productCount) }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm font-medium text-gray-500">Unidades disponibles</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($stockUnits) }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm font-medium text-gray-500">Movimientos de hoy</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($todayMovements) }}</p>
                    </div>
                    <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm font-medium text-gray-500">Categorías</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($totals->count()) }}</p>
                    </div>
                </div>
            </section>

            <div class="grid gap-8 xl:grid-cols-5">
                <section class="min-w-0 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 xl:col-span-3" aria-labelledby="recent-heading">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 id="recent-heading" class="text-lg font-semibold text-gray-900">Movimientos recientes</h3>
                            <p class="mt-1 text-sm text-gray-500">Últimos seis registros de stock.</p>
                        </div>
                        <a href="{{ route('stock.movements') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Ver historial completo</a>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th scope="col" class="px-3 py-3">Fecha</th>
                                    <th scope="col" class="px-3 py-3">Producto</th>
                                    <th scope="col" class="px-3 py-3">Tipo</th>
                                    <th scope="col" class="px-3 py-3 text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse ($recentMovements as $movement)
                                    <tr>
                                        <td class="px-3 py-4">{{ $movement->occurred_on->format('d/m/Y') }}</td>
                                        <td class="px-3 py-4 font-medium text-gray-900">{{ $movement->product?->name ?? 'Producto no disponible' }}</td>
                                        <td class="px-3 py-4">
                                            <span @class(['rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-green-100 text-green-800' => $movement->type === 'in', 'bg-red-100 text-red-800' => $movement->type === 'out'])>
                                                {{ $movement->type === 'in' ? 'Entrada' : 'Salida' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-right font-semibold">{{ number_format($movement->quantity) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-8 text-center text-gray-500">Aún no hay movimientos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 xl:col-span-2" aria-labelledby="categories-heading">
                    <h3 id="categories-heading" class="text-lg font-semibold text-gray-900">Stock por categoría</h3>
                    <div class="mt-4 flex flex-col gap-3">
                        @forelse ($totals as $total)
                            <div class="flex items-center justify-between gap-4 rounded-md bg-gray-50 px-4 py-3">
                                <span class="text-sm font-medium text-gray-700">{{ $total->category }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($total->units) }} unidades</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Aún no hay productos para resumir.</p>
                        @endforelse
                    </div>
                    <a href="{{ route('stock.products') }}" class="mt-6 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-900">Gestionar productos</a>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
