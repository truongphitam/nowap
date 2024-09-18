<?php

namespace Database\Seeders;

use App\Repositories\CompetitionsRepositories;
use App\Repositories\CountriesRepositories;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompetitionsTableSeeder extends Seeder
{
    protected CompetitionsRepositories $repository;
    public function __construct(
        CompetitionsRepositories $competitionsRepositories
    ){
        $this->repository = $competitionsRepositories;
    }

    public function run()
    {
        $jsonPath = public_path('assets/json/competitions.json');
        $jsonContent = file_get_contents($jsonPath);

        $data = json_decode($jsonContent, true);
        if($data){
            echo "--------- COMPETITIONS ---------".PHP_EOL;
            echo "BEGIN SYNC COMPETITIONS".PHP_EOL;
            foreach ($data as $item){
                if($item['league']['name'] && $item['league']['logo']){
                    $competition_name = $item['league']['name'];
                    echo "COMPETITIONS: $competition_name".PHP_EOL;
                    $competition = [
                        'name' => $competition_name,
                        'logo' => $item['league']['logo'],
                    ];

                    $this->repository->updateOrCreate($competition, $competition);
                }

            }
            echo "END SYNC COMPETITIONS".PHP_EOL;
        }
    }
}
