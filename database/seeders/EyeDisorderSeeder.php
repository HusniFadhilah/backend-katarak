<?php

namespace Database\Seeders;

use App\Models\EyeDisorder;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EyeDisorderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'name' => 'Mata kemeng',
            ],
            [
                'name' => 'Mata nyeri',
            ],
            [
                'name' => 'Mata silau',
            ],
            [
                'name' => 'Muncul putih-putih di mata',
            ],
            [
                'name' => 'Mata buram',
            ],
        ];
        foreach ($records as $record) {
            $data = new EyeDisorder();
            $data->firstOrCreate($record);
        }
    }
}
