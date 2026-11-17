<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Historial de movimientos</h2>
            <p class="mt-1 text-sm text-gray-500">Registrá entradas y salidas, y consultá el historial de stock.</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
            <x-validation-errors class="rounded-md border border-red-200 bg-red-50 p-4" />

            <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200" aria-labelledby="filters-heading">
                <h3 id="filters-heading" class="text-lg font-semibold text-gray-900">Filtrar historial</h3>
                <form method="GET" action="{{ route('stock.movements') }}" class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    <div>
                        <x-label for="q" value="Buscar producto" />
                        <x-input id="q" name="q" type="search" value="{{ request('q') }}" class="mt-1 block w-full" placeholder="SKU o nombre" />
                    </div>
                    <div>
                        <x-label for="type" value="Tipo" />
                        <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            <option value="in" @selected(request('type') === 'in')>Entrada</option>
                            <option value="out" @selected(request('type') === 'out')>Salida</option>
                        </select>
                    </div>
                    <div><x-label for="from" value="Desde" /><x-input id="from" name="from" type="date" value="{{ request('from') }}" class="mt-1 block w-full" /></div>
                    <div><x-label for="to" value="Hasta" /><x-input id="to" name="to" type="date" value="{{ request('to') }}" class="mt-1 block w-full" /></div>
                    <div class="flex items-end gap-3"><x-button>Aplicar filtros</x-button><a href="{{ route('stock.movements') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Limpiar</a></div>
                </form>
            </section>

            <div class="grid gap-8 xl:grid-cols-5">
                <section class="min-w-0 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 xl:col-span-3" aria-labelledby="history-heading">
                    <div class="flex items-center justify-between gap-4">
                        <h3 id="history-heading" class="text-lg font-semibold text-gray-900">Movimientos</h3>
                        <span class="text-sm text-gray-500">{{ $movements->total() }} registros</span>
                    </div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                            <thead class="text-xs uppercase tracking-wider text-gray-500"><tr><th scope="col" class="px-3 py-3">Fecha</th><th scope="col" class="px-3 py-3">Producto</th><th scope="col" class="px-3 py-3">Tipo</th><th scope="col" class="px-3 py-3 text-right">Cantidad</th><th scope="col" class="px-3 py-3">Detalle</th></tr></thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse ($movements as $movement)
                                    <tr>
                                        <td class="px-3 py-4">{{ $movement->occurred_on->format('d/m/Y') }}</td>
                                        <td class="px-3 py-4 font-medium text-gray-900">{{ $movement->product?->name ?? 'Producto no disponible' }}</td>
                                        <td class="px-3 py-4"><span @class(['rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-green-100 text-green-800' => $movement->type === 'in', 'bg-red-100 text-red-800' => $movement->type === 'out'])>{{ $movement->type === 'in' ? 'Entrada' : 'Salida' }}</span></td>
                                        <td class="px-3 py-4 text-right font-semibold">{{ number_format($movement->quantity) }}</td>
                                        <td class="px-3 py-4 text-gray-500">{{ $movement->type === 'in' ? $movement->supplier : $movement->reason }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No hay movimientos para los filtros seleccionados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $movements->links() }}</div>
                </section>

                <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 xl:col-span-2" aria-labelledby="movement-form-heading">
                    <h3 id="movement-form-heading" class="text-lg font-semibold text-gray-900">Registrar movimiento</h3>
                    <form method="POST" action="{{ route('movements.store') }}" x-data="{ type: '{{ old('type', 'in') }}' }" class="mt-4 flex flex-col gap-4">
                        @csrf
                        <div><x-label for="product_id" value="Producto" /><select id="product_id" name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">Seleccioná un producto</option>@foreach ($options as $option)<option value="{{ $option->id }}" @selected((string) old('product_id') === (string) $option->id)>{{ $option->sku }} — {{ $option->name }}</option>@endforeach</select><x-input-error for="product_id" class="mt-2" /></div>
                        <div><x-label for="movement_type" value="Tipo" /><select id="movement_type" name="type" x-model="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="in">Entrada</option><option value="out">Salida</option></select><x-input-error for="type" class="mt-2" /></div>
                        <div><x-label for="quantity" value="Cantidad" /><x-input id="quantity" name="quantity" type="number" min="1" max="99999" value="{{ old('quantity') }}" required class="mt-1 block w-full" /><x-input-error for="quantity" class="mt-2" /></div>
                        <div><x-label for="occurred_on" value="Fecha" /><x-input id="occurred_on" name="occurred_on" type="date" value="{{ old('occurred_on', today()->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" required class="mt-1 block w-full" /><x-input-error for="occurred_on" class="mt-2" /></div>
                        <div x-show="type === 'in'" x-cloak><x-label for="supplier" value="Proveedor" /><x-input id="supplier" name="supplier" type="text" maxlength="120" value="{{ old('supplier') }}" x-bind:required="type === 'in'" class="mt-1 block w-full" /><x-input-error for="supplier" class="mt-2" /></div>
                        <div x-show="type === 'out'" x-cloak><x-label for="reason" value="Motivo" /><x-input id="reason" name="reason" type="text" maxlength="100" value="{{ old('reason') }}" x-bind:required="type === 'out'" class="mt-1 block w-full" /><x-input-error for="reason" class="mt-2" /></div>
                        <div><x-button>Guardar movimiento</x-button></div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
