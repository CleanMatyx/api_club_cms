<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = ['Fútbol', 'Baloncesto', 'Tenis', 'Natación', 'Ciclismo', 'Paddle', 'Golf'];

        foreach ($sports as $sport) {
            Sport::create([
                'name' => $sport,
                'description' => "Descripción de $sport"
            ]);
        }
    }
}
