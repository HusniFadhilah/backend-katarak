<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'name' => 'Buruh/Tani',
            ],
            [
                'name' => 'IRT',
            ],
            [
                'name' => 'Pedagang',
            ],
            [
                'name' => 'Pensiunan',
            ],
            [
                'name' => 'PNS',
            ],
            [
                'name' => 'Pelajar/Mahasiswa',
            ],
            [
                'name' => 'Wiraswasta',
            ],
            [
                'name' => 'Tidak Bekerja',
            ],
            [
                'name' => 'Lainnya',
            ],
        ];
        foreach ($records as $record) {
            $data = new Job();
            $data->firstOrCreate($record);
        }
    }
}
