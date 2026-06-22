<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Rolls Clásicos',    'descripcion' => 'Rolls tradicionales japoneses',       'orden' => 1],
            ['nombre' => 'Rolls Especiales',  'descripcion' => 'Rolls con ingredientes premium',       'orden' => 2],
            ['nombre' => 'Rolls Tempura',     'descripcion' => 'Rolls con cobertura crocante',         'orden' => 3],
            ['nombre' => 'Nigiri & Sashimi',  'descripcion' => 'Piezas tradicionales de pescado',      'orden' => 4],
            ['nombre' => 'Entradas',          'descripcion' => 'Sopas, ensaladas y aperitivos',        'orden' => 5],
            ['nombre' => 'Combos',            'descripcion' => 'Combinaciones para compartir',         'orden' => 6],
            ['nombre' => 'Bebidas',           'descripcion' => 'Bebidas frías y calientes',            'orden' => 7],
        ];

        foreach ($categorias as $cat) {
            Categoria::create(array_merge($cat, ['activa' => true]));
        }
    }
}
