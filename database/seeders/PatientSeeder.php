<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'job_id' => 8,
                'created_by' => 3,
                'modificated_by' => 3,
                'name' => 'Tes Pasien',
                'ktp' => encrypt('111111'),
                'gender' => 'L',
                'birth_date' => Carbon::createFromDate(2000, 10, 15),
                'birth_place' => 'Semarang',
                'address' => 'Semarang'
            ],
        ];
        foreach ($records as $record) {
            $data = new Patient();
            $data->firstOrCreate($record);
        }
    }
}
