<?php

namespace Database\Seeders;

use App\Repositories\CountriesRepositories;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountriesTableSeeder extends Seeder
{
    protected CountriesRepositories $repository;
    public function __construct(
        CountriesRepositories $countriesRepositories
    ){
        $this->repository = $countriesRepositories;
    }

    public function run()
    {
        $jsonPath = public_path('assets/json/countries.json');
        $jsonContent = file_get_contents($jsonPath);

        $data = json_decode($jsonContent, true);
        if($data){
            echo "--------- COUNTRIES ---------".PHP_EOL;
            echo "BEGIN SYNC COUNTRIES".PHP_EOL;
            foreach ($data as $item){
                if($item['name'] && $item['flag']){
                    $country_name = $item['name'];
                    echo "COUNTRY: $country_name".PHP_EOL;
                    $country = [
                        'name' => $country_name,
                        'logo' => $item['flag'],
                    ];

                    $this->repository->updateOrCreate($country, $country);
                }

            }
            echo "END SYNC COUNTRIES".PHP_EOL;
        }
    }
}
