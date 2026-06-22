<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            ['nombre' => 'María González',   'telefono' => '+56912345678', 'email' => 'maria@email.com',   'direccion_principal' => 'Av. Providencia 1234, Santiago'],
            ['nombre' => 'Carlos Pérez',     'telefono' => '+56923456789', 'email' => 'carlos@email.com',  'direccion_principal' => 'Los Leones 567, Providencia'],
            ['nombre' => 'Ana Martínez',     'telefono' => '+56934567890', 'email' => 'ana@email.com',     'direccion_principal' => 'Irarrázaval 890, Ñuñoa'],
            ['nombre' => 'Pedro Soto',       'telefono' => '+56945678901', 'email' => null,                'direccion_principal' => 'Gran Avenida 2345, San Miguel'],
            ['nombre' => 'Valentina López',  'telefono' => '+56956789012', 'email' => 'vale@email.com',    'direccion_principal' => 'Av. Italia 456, Ñuñoa'],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
