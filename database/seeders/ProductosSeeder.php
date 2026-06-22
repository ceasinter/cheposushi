<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clasicos   = Categoria::where('nombre', 'Rolls Clásicos')->first()->id;
        $especiales = Categoria::where('nombre', 'Rolls Especiales')->first()->id;
        $tempura    = Categoria::where('nombre', 'Rolls Tempura')->first()->id;
        $nigiri     = Categoria::where('nombre', 'Nigiri & Sashimi')->first()->id;
        $entradas   = Categoria::where('nombre', 'Entradas')->first()->id;
        $combos     = Categoria::where('nombre', 'Combos')->first()->id;
        $bebidas    = Categoria::where('nombre', 'Bebidas')->first()->id;

        $productos = [
            // Rolls Clásicos
            ['categoria_id' => $clasicos, 'nombre' => 'California Roll',       'descripcion' => 'Cangrejo, palta, pepino',                    'precio' => 5990,  'orden' => 1],
            ['categoria_id' => $clasicos, 'nombre' => 'Spicy Tuna Roll',       'descripcion' => 'Atún, mayo picante, pepino',                 'precio' => 6490,  'orden' => 2],
            ['categoria_id' => $clasicos, 'nombre' => 'Salmon Roll',           'descripcion' => 'Salmón fresco, palta, queso crema',          'precio' => 6990,  'orden' => 3],
            ['categoria_id' => $clasicos, 'nombre' => 'Vegetariano Roll',      'descripcion' => 'Pepino, palta, zanahoria, espárrago',        'precio' => 4990,  'orden' => 4],
            ['categoria_id' => $clasicos, 'nombre' => 'Philadelphia Roll',     'descripcion' => 'Salmón ahumado, queso crema, palta',         'precio' => 6990,  'orden' => 5],

            // Rolls Especiales
            ['categoria_id' => $especiales, 'nombre' => 'Dragon Roll',         'descripcion' => 'Camarón tempura, palta, anguila',            'precio' => 8990,  'orden' => 1],
            ['categoria_id' => $especiales, 'nombre' => 'Rainbow Roll',        'descripcion' => 'Variedad de pescados sobre california',      'precio' => 9490,  'orden' => 2],
            ['categoria_id' => $especiales, 'nombre' => 'Volcano Roll',        'descripcion' => 'Cangrejo, mayo picante, gratinado',          'precio' => 8490,  'orden' => 3],
            ['categoria_id' => $especiales, 'nombre' => 'Spider Roll',         'descripcion' => 'Cangrejo blando, pepino, palta',             'precio' => 9990,  'orden' => 4],
            ['categoria_id' => $especiales, 'nombre' => 'Tuna Tataki Roll',    'descripcion' => 'Atún sellado, cebolla morada, ponzu',        'precio' => 9490,  'orden' => 5],

            // Rolls Tempura
            ['categoria_id' => $tempura, 'nombre' => 'Ebi Tempura Roll',       'descripcion' => 'Camarón tempura, palta, mayo',               'precio' => 7490,  'orden' => 1],
            ['categoria_id' => $tempura, 'nombre' => 'Salmon Tempura Roll',    'descripcion' => 'Salmón tempura, queso crema, ají',           'precio' => 7990,  'orden' => 2],
            ['categoria_id' => $tempura, 'nombre' => 'Crunchy Roll',           'descripcion' => 'Cobertura de panko, cangrejo, mayo',         'precio' => 7490,  'orden' => 3],

            // Nigiri & Sashimi
            ['categoria_id' => $nigiri, 'nombre' => 'Nigiri Salmón x2',        'descripcion' => 'Dos piezas de nigiri de salmón',             'precio' => 4490,  'orden' => 1],
            ['categoria_id' => $nigiri, 'nombre' => 'Nigiri Atún x2',          'descripcion' => 'Dos piezas de nigiri de atún',               'precio' => 4990,  'orden' => 2],
            ['categoria_id' => $nigiri, 'nombre' => 'Sashimi Salmón x5',       'descripcion' => 'Cinco láminas de salmón fresco',             'precio' => 6990,  'orden' => 3],
            ['categoria_id' => $nigiri, 'nombre' => 'Sashimi Mixto x8',        'descripcion' => 'Ocho láminas variadas del día',              'precio' => 9990,  'orden' => 4],

            // Entradas
            ['categoria_id' => $entradas, 'nombre' => 'Miso Soup',             'descripcion' => 'Sopa de miso con tofu y alga wakame',        'precio' => 2490,  'orden' => 1],
            ['categoria_id' => $entradas, 'nombre' => 'Edamame',               'descripcion' => 'Vainas de soya al vapor con sal marina',     'precio' => 2990,  'orden' => 2],
            ['categoria_id' => $entradas, 'nombre' => 'Gyozas x6',             'descripcion' => 'Dumplings de cerdo y verduras',              'precio' => 4990,  'orden' => 3],
            ['categoria_id' => $entradas, 'nombre' => 'Ensalada Wakame',        'descripcion' => 'Alga marina, sésamo, vinagreta ponzu',       'precio' => 3490,  'orden' => 4],
            ['categoria_id' => $entradas, 'nombre' => 'Takoyaki x6',           'descripcion' => 'Bolitas de pulpo con salsa especial',        'precio' => 4490,  'orden' => 5],

            // Combos
            ['categoria_id' => $combos, 'nombre' => 'Combo Dúo',               'descripcion' => '2 rolls clásicos + 2 misoshiru',             'precio' => 14990, 'orden' => 1],
            ['categoria_id' => $combos, 'nombre' => 'Combo Familia',           'descripcion' => '4 rolls a elección + 4 bebidas',             'precio' => 27990, 'orden' => 2],
            ['categoria_id' => $combos, 'nombre' => 'Combo Ejecutivo',         'descripcion' => '1 roll especial + miso soup + bebida',       'precio' => 11990, 'orden' => 3],
            ['categoria_id' => $combos, 'nombre' => 'Combo Sashimi & Roll',    'descripcion' => 'Sashimi mixto x5 + 1 roll clásico',         'precio' => 13990, 'orden' => 4],

            // Bebidas
            ['categoria_id' => $bebidas, 'nombre' => 'Agua Mineral 500ml',     'descripcion' => 'Con o sin gas',                             'precio' => 1490,  'orden' => 1],
            ['categoria_id' => $bebidas, 'nombre' => 'Bebida lata 350ml',      'descripcion' => 'Coca-Cola, Sprite, Fanta',                   'precio' => 1690,  'orden' => 2],
            ['categoria_id' => $bebidas, 'nombre' => 'Jugo Natural 400ml',     'descripcion' => 'Naranja, mango o maracuyá',                  'precio' => 2490,  'orden' => 3],
            ['categoria_id' => $bebidas, 'nombre' => 'Té Verde Caliente',      'descripcion' => 'Té verde japonés tradicional',               'precio' => 1990,  'orden' => 4],
            ['categoria_id' => $bebidas, 'nombre' => 'Sake 180ml',             'descripcion' => 'Sake japonés frío o caliente',               'precio' => 3990,  'orden' => 5],
        ];

        foreach ($productos as $prod) {
            Producto::create(array_merge($prod, ['disponible' => true]));
        }
    }
}
