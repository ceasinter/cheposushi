<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'operador']);
        Role::create(['name' => 'cocinero']);
        Role::create(['name' => 'repartidor']);

        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@cheposushi.cl',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $operador = User::create([
            'name'     => 'Operador POS',
            'email'    => 'operador@cheposushi.cl',
            'password' => bcrypt('password'),
        ]);
        $operador->assignRole('operador');

        $repartidor = User::create([
            'name'     => 'Repartidor 1',
            'email'    => 'repartidor@cheposushi.cl',
            'password' => bcrypt('password'),
        ]);
        $repartidor->assignRole('repartidor');
    }
}
