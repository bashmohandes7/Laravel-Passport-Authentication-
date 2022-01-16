<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
        // call permissions seeder first then call admin to assign these permissions to you
        $this->call([
            PermissionDemoSeeder::class,
            AdminSeeder::class
        ]);
    }
}
