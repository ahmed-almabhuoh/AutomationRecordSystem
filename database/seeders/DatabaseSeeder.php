<?php

namespace Database\Seeders;

use App\Models\Manager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // Manager::factory(100)->create();
        // Manager::create([
        //     'fname' => 'Super',
        //     'sname' => 'Super',
        //     'tname' => 'Super',
        //     'lname' => 'Super',
        //     'email' => 'super@auto.com.ps',
        //     'identity_no' => '123456789',
        //     'phone' => '0567077653',
        //     'password' => Hash::make('password'),
        //     'gender' => 'male',
        //     'status' => 'active',
        // ]);

        $manager = new Manager();
        $manager->fname = 'Super';
        $manager->sname = 'Super';
        $manager->tname = 'Super';
        $manager->lname = 'Super';
        $manager->email = 'super@auto.com.ps';
        $manager->identity_no = '123456789';
        $manager->phone = '0567077653';
        $manager->gender = 'male';
        $manager->status = 'active';
        $manager->password = Hash::make('password');
        $manager->save();
    }
}
