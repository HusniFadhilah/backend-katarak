<?php

namespace Database\Seeders;

use App\Models\PastMedical;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PastMedicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'name' => 'Riwayat penggunaan kacamata',
            ],
            [
                'name' => 'Riwayat operasi mata',
            ],
            [
                'name' => 'Riwayat trauma mata',
            ],
            [
                'name' => 'Hipertensi',
            ],
            [
                'name' => 'Hipertensi',
            ],
        ];
        foreach ($records as $record) {
            $data = new PastMedical();
            $data->firstOrCreate($record);
        }
    }
}
