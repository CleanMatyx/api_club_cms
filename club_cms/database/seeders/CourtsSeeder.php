<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sport;
use App\Models\Court;

class CourtsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deportes de ejemplo que tengo en el factory de Sports
        $sports = ['Fútbol', 'Baloncesto', 'Tenis', 'Natación', 'Ciclismo', 'Paddle', 'Golf'];

        foreach ($sports as $sportName) {
            $sport = Sport::firstOrCreate(['name' => $sportName]);

            if (in_array($sportName, ['Natación', 'Golf'])) {
                $location = $sportName === 'Natación' ? 'Piscinas Públicas' : 'Campo de Golf El Verde';
            } else {
                $location = in_array($sportName, ['Fútbol', 'Baloncesto', 'Tenis', 'Ciclismo', 'Paddle']) ? 'Club Deportivo Agujetas' : 'Polideportivo El Sudor';
            }

            $randomNumber = rand(2, 6);

            for ($i = 1; $i <= $randomNumber; $i++) {
                Court::create([
                    'sport_id' => $sport->id,
                    'name' => "Pista de $sportName 0$i",
                    'location' => $location,
                ]);
            }
        }
    }
}
