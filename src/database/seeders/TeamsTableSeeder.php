<?php

namespace Database\Seeders;

use App\Repositories\CompetitionsRepositories;
use App\Repositories\CountriesRepositories;
use App\Repositories\TeamsRepositories;
use App\Services\FootballApiService;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeamsTableSeeder extends Seeder
{
    protected FootballApiService $footballApiService;
    protected CompetitionsRepositories $competitionsRepositories;
    protected CountriesRepositories $countriesRepositories;
    protected TeamsRepositories $teamsRepositories;

    public function __construct(
        FootballApiService $footballApiService,
        CompetitionsRepositories $competitionsRepositories,
        CountriesRepositories $countriesRepositories,
        TeamsRepositories $teamsRepositories
    ) {
        $this->footballApiService = $footballApiService;
        $this->competitionsRepositories = $competitionsRepositories;
        $this->countriesRepositories = $countriesRepositories;
        $this->teamsRepositories = $teamsRepositories;
    }

    public function run()
    {
        $competitions = getCompetitions();
        foreach ($competitions as $_item) {
            $leagueId = $_item['id'];
            $jsonPath = public_path("assets/json/teams-$leagueId.json");
            $jsonContent = file_get_contents($jsonPath);

            $data = json_decode($jsonContent, true);
            if($data){
                foreach ($data as $item){
                    $competition_name = $_item['name'];
                    $country_name = $item['team']['country'];
                    $competition = $this->competitionsRepositories->findWhere(['name' => $competition_name])->first();
                    $country = $this->countriesRepositories->findWhere(['name' =>  $country_name])->first();
                    $team_name = $item['team']['name'];
                    if($competition && $country){
                        echo "- COMPETITION: $competition_name - COUNTRY: $country_name - TEAM: $team_name" . PHP_EOL;

                        $values = [
                            'competition_id' => $competition->id,
                            'country_id' => $country->id,
                            'logo' => $item['team']['logo'],
                            'name' => $team_name
                        ];

                        $this->teamsRepositories->updateOrCreate(
                            [
                                'name' => $team_name
                            ],
                            $values
                        );
                    }
                }
            }
        }
    }
}
