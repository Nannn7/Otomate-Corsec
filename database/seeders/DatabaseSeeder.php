<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Basicdata\Database\Seeders\BasicdataDatabaseSeeder;
use Modules\Corsec\Database\Seeders\CorsecDatabaseSeeder;
use Modules\Logs\Database\Seeders\LogsDatabaseSeeder;
use Modules\Usermanagement\Database\Seeders\UsermanagementDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            BasicdataDatabaseSeeder::class,
            LogsDatabaseSeeder::class,
            CorsecDatabaseSeeder::class,
            UsermanagementDatabaseSeeder::class,
        ]);
    }
}
