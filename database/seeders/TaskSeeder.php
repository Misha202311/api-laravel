<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = DB::table('users')->get();

        foreach ($users as $user){
            DB::table('tasks')->insert([
                'title' => Str::random(10),
                'description' => Str::random(30),
                'user_id' => $user->id,
            ]);
        }


    }
}
