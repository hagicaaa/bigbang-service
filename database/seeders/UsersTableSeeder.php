<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bigbang.com',
            'password' => bcrypt('12341234')
        ]);
        User::create([
            'name' => 'QC',
            'email' => 'qc@bigbang.com',
            'password' => bcrypt('12341234')
        ]);
        User::create([
            'name' => 'Technician',
            'email' => 'tech@bigbang.com',
            'password' => bcrypt('12341234')
        ]);

        $admin = User::find(1);
        $admin->assignRole('admin');
        $qc = User::find(2);
        $qc->assignRole('qc');
        $tech = User::find(3);
        $tech->assignRole('technician');
    }
}
