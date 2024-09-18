<?php

namespace App\Services;

use App\Models\Competitions;
use App\Models\Countries;
use App\Models\Competition;
use App\Models\Matches;
use App\Models\Teams;
use App\Repositories\CompetitionsRepositories;
use App\Repositories\CountriesRepositories;
use App\Repositories\MatchesRepositories;
use App\Repositories\TeamsRepositories;
use Illuminate\Support\Facades\Http;

class FootballApiService
{
    protected $apiUrl = '';
    protected $headers = [];
    protected $countriesRepositories;
    protected $competitionsRepositories;
    protected $teamsRepositories;
    protected $matchesRepositories;

    public function __construct(
        CountriesRepositories $countriesRepositories,
        CompetitionsRepositories $competitionsRepositories,
        TeamsRepositories $teamsRepositories,
        MatchesRepositories $matchesRepositories
    ) {
        $this->apiUrl = env('R_ENDPOINT', 'https://api-football-v1.p.rapidapi.com/v3/');
        $this->headers = [
            'x-rapidapi-host' => 'api-football-v1.p.rapidapi.com',
            'x-rapidapi-key' => env('R_API_KEY'),
        ];

        $this->countriesRepositories = $countriesRepositories;
        $this->competitionsRepositories = $competitionsRepositories;
        $this->teamsRepositories = $teamsRepositories;
        $this->matchesRepositories = $matchesRepositories;
    }

    public function fetchCountries(): void
    {
        $response = Http::withHeaders($this->headers)
            ->get($this->apiUrl . 'countries');
        $countries = $response->json()['response'];

        foreach ($countries as $country) {
            Countries::updateOrCreate(
                ['name' => $country['name']],
                ['logo' => $country['flag']]
            );
        }
    }

    public function fetchCompetitions(): void
    {
        $response = Http::withHeaders($this->headers)
            ->get($this->apiUrl . 'leagues');
        $competitions = $response->json()['response'];

        foreach ($competitions as $competition) {
            Competitions::updateOrCreate(
                ['name' => $competition['league']['name']],
                ['logo' => $competition['league']['logo']]
            );
        }
    }

    public function fetchTeams($leagueId, $competitionId): void
    {
        $response = Http::withHeaders($this->headers)
            ->get($this->apiUrl . 'teams?league=' . $leagueId . '&season=2024');
        $teams = $response->json()['response'];
        if ($teams) {
            foreach ($teams as $team) {
                $country_name = $team['team']['country'];
                $team_name = $team['team']['name'];
                $country = $this->countriesRepositories->findWhere(['name' =>  $country_name])->first();
                if ($country) {
                    echo ("- COUNTRY: $country_name - Team: $team_name" . PHP_EOL);
                    $values = [
                        'competition_id' => $competitionId,
                        'country_id' => $country->id,
                        'logo' => $team['team']['logo'],
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

    public function fetchMatches($leagueId, $competitionId): void
    {
        $response = Http::withHeaders($this->headers)
            ->get($this->apiUrl . 'fixtures?league=' . $leagueId . '&season=2024');
        $matches = $response->json()['response'];

        foreach ($matches as $match) {
            Matches::create([
                'competition_id' => $competitionId,
                'home_team_id' => Teams::where('name', $match['teams']['home']['name'])->first()->id,
                'away_team_id' => Teams::where('name', $match['teams']['away']['name'])->first()->id,
                'status_id' => $match['fixture']['status']['short'],
                'match_time' => $match['fixture']['date'],
                'home_scores' => [
                    'fulltime' => $match['goals']['home'],
                    'halftime' => $match['score']['halftime']['home'],
                ],
                'away_scores' => [
                    'fulltime' => $match['goals']['away'],
                    'halftime' => $match['score']['halftime']['away'],
                ],
            ]);
        }
    }
}
