<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Member;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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
                'role_id' => 1,
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('secret1234'),
            ],
            [
                'role_id' => 2,
                'name' => 'Dokter',
                'email' => 'doctor@gmail.com',
                'password' => bcrypt('secret1234'),
            ],
            [
                'role_id' => 3,
                'name' => 'Kader',
                'email' => 'kader@gmail.com',
                'password' => bcrypt('secret1234'),
            ],
        ];
        foreach ($records as $record) {
            $user = new User();
            $user = $user->firstOrCreate($record);
        }
    }
}
