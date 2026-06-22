<?php

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\DetalleDelivery;
use App\Models\Comanda;
use Livewire\Volt\Component;

new class extends Component {

    public array $carrito = [];
    public string $tipo = 'retiro';
    public ?int $cliente_id = null;
    public string $nombre_cliente = '';
    public string $telefono_cliente = '';
    public string $direccion = '';
    public string $referencia = '';
    public string $comuna = '';
    public string $metodo_pago = 'efectivo';
    public string $monto_pagado = '';
    public int $categoria_activa = 0;
    public string $busqueda = '';
    public string $notas = '';
    public bool $modal_confirmar = false;
    public bool $modal_cliente = false;
    public string $busqueda_cliente = '';
    public array $resultados_cliente = [];

    public function mount(): void
    {
        $primera = Categoria::where('activa', true)->orderBy('orden')->first();
        $this->categoria_activa = $primera?->id ?? 0;
    }

    public function with(): array
    {
        $productos = Producto::query()
            ->where('disponible', true)
            ->when($this->busqueda, fn($q) =>
                $q->where('nombre', 'like', "%{$this->busqueda}%")
            )
            ->when(!$this->busqueda && $this->categoria_activa, fn($q) =>
                $q->where('categoria_id', $this->categoria_activa)
            )
            ->orderBy('orden')
            ->get();

        $categorias = Categoria::where('activa', true)->orderBy('orden')->get();
        $subtotal = collect($this->carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        $total = $subtotal;
        $cambio = max(0, (float) $this->monto_pagado - $total);
        $cantidad_items = collect($this->carrito)->sum('cantidad');

        return compact('productos', 'categorias', 'subtotal', 'total', 'cambio', 'cantidad_items');
    }

    public function agregarProducto(int $id): void
    {
        if (isset($this->carrito[$id])) {
            $this->carrito[$id]['cantidad']++;
        } else {
            $producto = Producto::find($id);
            $this->carrito[$id] = [
                'id'       => $producto->id,
                'nombre'   => $producto->nombre,
                'precio'   => (float) $producto->precio,
                'cantidad' => 1,
                'notas'    => '',
            ];
        }
    }

    public function incrementar(int $id): void
    {
        if (isset($this->carrito[$id])) {
            $this->carrito[$id]['cantidad']++;
        }
    }

    public function decrementar(int $id): void
    {
        if (isset($this->carrito[$id])) {
            $this->carrito[$id]['cantidad'] > 1
                ? $this->carrito[$id]['cantidad']--
                : $this->eliminarItem($id);
        }
    }

    public function eliminarItem(int $id): void
    {
        unset($this->carrito[$id]);
    }

    public function limpiarCarrito(): void
    {
        $this->carrito          = [];
        $this->cliente_id       = null;
        $this->nombre_cliente   = '';
        $this->telefono_cliente = '';
        $this->direccion        = '';
        $this->referencia       = '';
        $this->comuna           = '';
        $this->notas            = '';
        $this->monto_pagado     = '';
        $this->tipo             = 'retiro';
    }

    public function buscarCliente(): void
    {
        if (strlen($this->busqueda_cliente) < 2) {
            $this->resultados_cliente = [];
            return;
        }

        $this->resultados_cliente = Cliente::where('nombre', 'like', "%{$this->busqueda_cliente}%")
            ->orWhere('telefono', 'like', "%{$this->busqueda_cliente}%")
            ->limit(5)
            ->get(['id', 'nombre', 'telefono', 'direccion_principal'])
            ->toArray();
    }

    public function seleccionarCliente(int $id): void
    {
        $cliente = Cliente::find($id);
        $this->cliente_id       = $cliente->id;
        $this->nombre_cliente   = $cliente->nombre;
        $this->telefono_cliente = $cliente->telefono;

        if ($this->tipo === 'delivery' && $cliente->direccion_principal) {
            $this->direccion = $cliente->direccion_principal;
        }

        $this->modal_cliente      = false;
        $this->busqueda_cliente   = '';
        $this->resultados_cliente = [];
    }

    public function abrirModalConfirmar(): void
    {
        if (empty($this->carrito)) return;
        $this->modal_confirmar = true;
    }

    public function confirmarPedido(): void
    {
        $this->validate([
            'tipo'           => 'required|in:delivery,retiro',
            'metodo_pago'    => 'required|in:efectivo,tarjeta,transferencia',
            'nombre_cliente' => 'required_without:cliente_id|string|max:100',
            'direccion'      => 'required_if:tipo,delivery',
        ], [
            'nombre_cliente.required_without' => 'Ingresa el nombre del cliente.',
            'direccion.required_if'           => 'La direccion es obligatoria para delivery.',
        ]);

        $subtotal = collect($this->carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);

        $pedido = Pedido::create([
            'numero'           => Pedido::generarNumero(),
            'tipo'             => $this->tipo,
            'estado'           => Pedido::ESTADO_PENDIENTE,
            'cliente_id'       => $this->cliente_id,
            'nombre_cliente'   => $this->nombre_cliente,
            'telefono_cliente' => $this->telefono_cliente,
            'subtotal'         => $subtotal,
            'descuento'        => 0,
            'total'            => $subtotal,
            'metodo_pago'      => $this->metodo_pago,
            'monto_pagado'     => $this->monto_pagado ?: null,
            'notas'            => $this->notas,
            'usuario_id'       => auth()->id(),
        ]);

        foreach ($this->carrito as $item) {
            PedidoItem::create([
                'pedido_id'       => $pedido->id,
                'producto_id'     => $item['id'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio'],
                'subtotal'        => $item['precio'] * $item['cantidad'],
                'notas'           => $item['notas'] ?? '',
            ]);
        }

        if ($this->tipo === 'delivery') {
            DetalleDelivery::create([
                'pedido_id'  => $pedido->id,
                'direccion'  => $this->direccion,
                'referencia' => $this->referencia,
                'comuna'     => $this->comuna,
                'estado'     => DetalleDelivery::ESTADO_SIN_ASIGNAR,
            ]);
        }

        $comanda = Comanda::create([
            'pedido_id'      => $pedido->id,
            'numero_comanda' => Comanda::siguienteNumero(),
        ]);

        $this->modal_confirmar = false;
        $this->limpiarCarrito();

        $this->redirect(route('comanda.imprimir', $comanda->id));
    }

}; ?>

<div class="flex h-screen bg-gray-100 select-none">

    <div class="flex flex-col flex-1 min-w-0">

        <div class="bg-white border-b px-4 py-3 flex items-center gap-3">
            <span class="font-bold text-lg text-gray-800">Chepo Sushi POS</span>
            <div class="flex-1">
                <input
                    wire:model.live.debounce.300ms="busqueda"
                    type="text"
                    placeholder="Buscar producto..."
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                >
            </div>
            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
        </div>

        <div class="bg-white border-b px-4 py-2 flex gap-2 overflow-x-auto">
            @foreach($categorias as $cat)
                <button
                    wire:click="$set('categoria_activa', {{ $cat->id }})"
                    class="whitespace-nowrap px-3 py-1.5 rounded-full text-sm font-medium transition
                        {{ $categoria_activa === $cat->id
                            ? 'bg-orange-500 text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-orange-100' }}"
                >
                    {{ $cat->nombre }}
                </button>
            @endforeach
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @forelse($productos as $producto)
                    <button
                        wire:click="agregarProducto({{ $producto->id }})"
                        class="bg-white rounded-xl p-3 text-left shadow-sm hover:shadow-md hover:ring-2 hover:ring-orange-400 transition active:scale-95"
                    >
                        <div class="text-sm font-semibold text-gray-800 leading-tight">
                            {{ $producto->nombre }}
                        </div>
                        <div class="text-xs text-gray-400 mt-1 line-clamp-1">
                            {{ $producto->descripcion }}
                        </div>
                        <div class="text-orange-600 font-bold mt-2 text-sm">
                            ${{ number_format($producto->precio, 0, ',', '.') }}
                        </div>
                    </button>
                @empty
                    <div class="col-span-4 text-center text-gray-400 py-16">
                        No hay productos disponibles
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="w-96 bg-white border-l flex flex-col shadow-xl">

        <div class="p-4 border-b">
            <div class="flex rounded-lg overflow-hidden border">
                <button
                    wire:click="$set('tipo', 'retiro')"
                    class="flex-1 py-2 text-sm font-medium transition
                        {{ $tipo === 'retiro' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                >
                    Retiro
                </button>
                <button
                    wire:click="$set('tipo', 'delivery')"
                    class="flex-1 py-2 text-sm font-medium transition
                        {{ $tipo === 'delivery' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}"
                >
                    Delivery
                </button>
            </div>
        </div>

        <div class="px-4 py-3 border-b">
            @if($nombre_cliente)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $nombre_cliente }}</p>
                        <p class="text-xs text-gray-400">{{ $telefono_cliente }}</p>
                    </div>
                    <button wire:click="$set('nombre_cliente', '')" class="text-gray-400 hover:text-red-500 text-xs">X</button>
                </div>
            @else
                <div class="flex gap-2">
                    <input
                        wire:model="nombre_cliente"
                        type="text"
                        placeholder="Nombre cliente *"
                        class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    <button
                        wire:click="$set('modal_cliente', true)"
                        class="bg-gray-100 hover:bg-gray-200 px-3 rounded-lg text-sm text-gray-600"
                    >
                        Buscar
                    </button>
                </div>
                <input
                    wire:model="telefono_cliente"
                    type="text"
                    placeholder="Telefono"
                    class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                >
            @endif

            @if($tipo === 'delivery')
                <input
                    wire:model="direccion"
                    type="text"
                    placeholder="Direccion *"
                    class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                >
                <div class="flex gap-2 mt-2">
                    <input
                        wire:model="comuna"
                        type="text"
                        placeholder="Comuna"
                        class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    <input
                        wire:model="referencia"
                        type="text"
                        placeholder="Referencia"
                        class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>
            @endif
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-2">
            @forelse($carrito as $id => $item)
                <div class="flex items-center gap-2 py-2 border-b last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $item['nombre'] }}</p>
                        <p class="text-xs text-gray-400">${{ number_format($item['precio'], 0, ',', '.') }} c/u</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            wire:click="decrementar({{ $id }})"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 font-bold flex items-center justify-center"
                        >-</button>
                        <span class="w-6 text-center text-sm font-semibold">{{ $item['cantidad'] }}</span>
                        <button
                            wire:click="incrementar({{ $id }})"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-orange-100 text-gray-600 hover:text-orange-600 font-bold flex items-center justify-center"
                        >+</button>
                    </div>
                    <div class="text-sm font-bold text-gray-800 w-16 text-right">
                        ${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-300 py-12 text-sm">
                    Agrega productos al pedido
                </div>
            @endforelse
        </div>

        <div class="px-4 pb-2">
            <input
                wire:model="notas"
                type="text"
                placeholder="Notas del pedido (opcional)"
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
            >
        </div>

        <div class="p-4 border-t bg-gray-50">
            <div class="flex justify-between text-sm text-gray-600 mb-1">
                <span>Subtotal ({{ $cantidad_items }} items)</span>
                <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-gray-900 mb-3">
                <span>Total</span>
                <span class="text-orange-600">${{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <div class="flex gap-1 mb-3">
                @foreach(['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transfer'] as $valor => $label)
                    <button
                        wire:click="$set('metodo_pago', '{{ $valor }}')"
                        class="flex-1 py-1.5 text-xs font-medium rounded-lg transition
                            {{ $metodo_pago === $valor ? 'bg-orange-500 text-white' : 'bg-white border text-gray-600 hover:bg-orange-50' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if($metodo_pago === 'efectivo')
                <div class="flex gap-2 mb-3 items-center">
                    <input
                        wire:model.live="monto_pagado"
                        type="number"
                        placeholder="Monto recibido"
                        class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                    @if($cambio > 0)
                        <div class="text-sm font-bold text-green-600 whitespace-nowrap">
                            Vuelto ${{ number_format($cambio, 0, ',', '.') }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex gap-2">
                <button
                    wire:click="limpiarCarrito"
                    class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-medium text-sm transition"
                >
                    Limpiar
                </button>
                <button
                    wire:click="abrirModalConfirmar"
                    @if(empty($carrito)) disabled @endif
                    class="flex-1 py-3 bg-orange-500 hover:bg-orange-600 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl font-bold text-sm transition"
                >
                    Confirmar pedido
                </button>
            </div>
        </div>
    </div>

    @if($modal_confirmar)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Confirmar pedido</h2>

                <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                    @foreach($carrito as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-700">{{ $item['cantidad'] }}x {{ $item['nombre'] }}</span>
                            <span class="font-medium">${{ number_format($item['precio'] * $item['cantidad'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-3 mb-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tipo</span>
                        <span class="font-medium capitalize">{{ $tipo }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Cliente</span>
                        <span class="font-medium">{{ $nombre_cliente ?: 'Sin nombre' }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Pago</span>
                        <span class="font-medium capitalize">{{ $metodo_pago }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-base mt-2">
                        <span>Total</span>
                        <span class="text-orange-600">${{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        wire:click="$set('modal_confirmar', false)"
                        class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="confirmarPedido"
                        class="flex-1 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold"
                    >
                        Confirmar e imprimir
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($modal_cliente)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-sm shadow-2xl">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Buscar cliente</h2>
                <input
                    wire:model.live.debounce.300ms="busqueda_cliente"
                    wire:keyup="buscarCliente"
                    type="text"
                    placeholder="Nombre o telefono..."
                    class="w-full border rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    autofocus
                >
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @forelse($resultados_cliente as $c)
                        <button
                            wire:click="seleccionarCliente({{ $c['id'] }})"
                            class="w-full text-left px-3 py-2 rounded-lg hover:bg-orange-50 border transition"
                        >
                            <p class="text-sm font-semibold text-gray-800">{{ $c['nombre'] }}</p>
                            <p class="text-xs text-gray-400">{{ $c['telefono'] }}</p>
                        </button>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Sin resultados</p>
                    @endforelse
                </div>
                <button
                    wire:click="$set('modal_cliente', false)"
                    class="mt-4 w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm"
                >
                    Cerrar
                </button>
            </div>
        </div>
    @endif

</div>
