<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory(9)->create();

        $user = \App\Models\User::factory() -> create([
            'name' => 'Admin Faisal',
            'email' => 'faisal@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' =>  '085712345678',
            'roles' => 'ADMIN',
            'address' => 'Pondok Alam Permai Purati',
        ]);
    }
}
