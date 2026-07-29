<?php

namespace Modules\Journals\Database\Seeders;

use Illuminate\Database\Seeder;

class JournalsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $this->call(PermissionSeeder::class);
        $this->call(JournalDummySeeder::class);
    }
}
