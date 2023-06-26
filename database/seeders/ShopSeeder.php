<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shops')->insertGetId([
            'title' => 'Комус',
            'url' => 'https://comus.ru',
            'created_at' => DB::raw('NOW()'),
        ]);
    }
}
