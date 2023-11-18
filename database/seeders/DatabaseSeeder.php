<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "Admin",
            'email' => "admin@mail.ru",
            'email_verified_at' => now(),
            'password' => '$2y$10$o2LH0X7jZHbFgfFmvdTIpe.UmYeX6REbIsmZWjtwqD7DSGlsEmZ72', // password
            'remember_token' => Str::random(10),
            'role' => "Administrator",
        ]);
        \App\Models\User::factory(10)->create();

    }
}
