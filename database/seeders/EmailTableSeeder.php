<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Email;
class EmailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Email::factory(50)->create();
    }
}
