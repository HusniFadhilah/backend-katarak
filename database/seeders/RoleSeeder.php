<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            [
                'name' => 'admin',
                'alias' => 'Admin',
            ],
            [
                'name' => 'doctor',
                'alias' => 'Dokter'
            ],
            [
                'name' => 'kader',
                'alias' => 'Kader'
            ],
        ];
        foreach ($records as $record) {
            $data = new Role();
            $data->firstOrCreate($record);
        }
    }
}
