<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Municipalities extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $municipalities = [
            [
                'municipality_name' => 'Nabunturan',
                'color' => '#FF5733',
                'report_threshold' => 100,
                'lat' => 7.6079,
                'lng' => 125.9551,
            ],
            [
                'municipality_name' => 'Monkayo',
                'color' => '#33FF57',
                'report_threshold' => 100,
                'lat' => 7.8214,
                'lng' => 126.0543,
            ],
            [
                'municipality_name' => 'Mawab',
                'color' => '#3357FF',
                'report_threshold' => 100,
                'lat' => 7.5436,
                'lng' => 125.9915,
            ],
            [
                'municipality_name' => 'Pantukan',
                'color' => '#FF33A1',
                'report_threshold' => 100,
                'lat' => 7.2792,
                'lng' => 125.8937,
            ],
            [
                'municipality_name' => 'Maco',
                'color' => '#FFA133',
                'report_threshold' => 100,
                'lat' => 7.3598,
                'lng' => 125.8458,
            ],
            [
                'municipality_name' => 'Maragusan',
                'color' => '#33FFF5',
                'report_threshold' => 100,
                'lat' => 7.3090,
                'lng' => 126.1535,
            ],
            [
                'municipality_name' => 'Compostela',
                'color' => '#F5FF33',
                'report_threshold' => 100,
                'lat' => 7.6853,
                'lng' => 126.0901,
            ],
            [
                'municipality_name' => 'Laak',
                'color' => '#FF5733',
                'report_threshold' => 100,
                'lat' => 7.8589,
                'lng' => 125.7466,
            ],
            [
                'municipality_name' => 'Mabini',
                'color' => '#33FFA8',
                'report_threshold' => 100,
                'lat' => 7.1577,
                'lng' => 125.9445,
            ],
            [
                'municipality_name' => 'New Bataan',
                'color' => '#A833FF',
                'report_threshold' => 100,
                'lat' => 7.5634,
                'lng' => 126.2475,
            ],
            [
                'municipality_name' => 'Motevista',
                'color' => '#A833FF',
                'report_threshold' => 100,
                'lat' => 7.7035,
                'lng' => 125.98801,
            ],
        ];

        // Insert data into the municipalities table
        DB::table('municipalities')->insert($municipalities);
    }
}
