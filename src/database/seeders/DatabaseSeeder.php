<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Repositories\MatchesRepositories;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(CompetitionsTableSeeder::class);
        $this->call(TeamsTableSeeder::class);
        $this->call(MatchesTableSeeder::class);
    }
}
