<?php

namespace Database\Seeders;

use App\Repositories\CompetitionsRepositories;
use App\Repositories\CountriesRepositories;
use App\Repositories\MatchesRepositories;
use App\Repositories\TeamsRepositories;
use App\Services\FootballApiService;
use GuzzleHttp\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatchesTableSeeder extends Seeder
{
    protected FootballApiService $footballApiService;
    protected CompetitionsRepositories $competitionsRepositories;
    protected CountriesRepositories $countriesRepositories;
    protected TeamsRepositories $teamsRepositories;
    protected MatchesRepositories $matchesRepositories;

    public function __construct(
        FootballApiService $footballApiService,
        CompetitionsRepositories $competitionsRepositories,
        CountriesRepositories $countriesRepositories,
        TeamsRepositories $teamsRepositories,
        MatchesRepositories $matchesRepositories
    ) {
        $this->footballApiService = $footballApiService;
        $this->competitionsRepositories = $competitionsRepositories;
        $this->countriesRepositories = $countriesRepositories;
        $this->teamsRepositories = $teamsRepositories;
        $this->matchesRepositories = $matchesRepositories;
    }

    public function run()
    {
        $competitions = getCompetitions();
        foreach ($competitions as $_item) {
            $leagueId = $_item['id'];
            $jsonPath = public_path("assets/json/matches-$leagueId.json");
            $jsonContent = file_get_contents($jsonPath);

            $data = json_decode($jsonContent, true);
            if ($data) {
                foreach ($data as $item) {
                    $competition_name = $_item['name'];
                    $team_home_name = $item['teams']['home']['name'];
                    $team_away_name = $item['teams']['away']['name'];

                    $team_home = $this->teamsRepositories->findWhere(['name' => $team_home_name])->first();
                    $team_away = $this->teamsRepositories->findWhere(['name' => $team_away_name])->first();
                    $competition = $this->competitionsRepositories->findWhere(['name' => $competition_name])->first();
                    $team_home_score = $item['goals']['home'] ?? 0;
                    $team_away_score = $item['goals']['away'] ?? 0;

                    if ($competition && $team_home && $team_away) {
                        echo "$competition_name: $team_away_name ($team_home_score) - $team_away_name ($team_away_score)".PHP_EOL;
                        $home_scores = getScores($item);
                        $away_scores = getScores($item, 'away');
                        $values = [
                            'competition_id' => $competition->id,
                            'home_team_id' => $team_home->id,
                            'away_team_id' => $team_away->id,
                            'status_id' => rand(1, 9),
                            'match_time' => $item['fixture']['timestamp'],
                            'home_scores' => $home_scores,
                            'away_scores' => $away_scores
                        ];


                        $this->matchesRepositories->updateOrCreate(
                            [
                                'competition_id' => $competition->id,
                                'home_team_id' => $team_home->id,
                                'away_team_id' => $team_away->id,
                            ],
                            $values
                        );
                    }
                }
            }
        }
    }
}
