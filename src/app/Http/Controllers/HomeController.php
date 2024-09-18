<?php

namespace App\Http\Controllers;

use App\Http\Transformers\CompetitionCollection;
use App\Repositories\CompetitionsRepositories;
use App\Repositories\CountriesRepositories;
use App\Repositories\MatchesRepositories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    //
    protected CountriesRepositories $countriesRepositories;
    protected MatchesRepositories $matchesRepositories;
    protected CompetitionsRepositories $competitionsRepositories;

    public function __construct(
        CountriesRepositories    $countriesRepositories,
        MatchesRepositories      $matchesRepositories,
        CompetitionsRepositories $competitionsRepositories
    )
    {
        $this->countriesRepositories = $countriesRepositories;
        $this->matchesRepositories = $matchesRepositories;
        $this->competitionsRepositories = $competitionsRepositories;
    }

    public function index()
    {
        // mockup data for show
        $statusMockIds = [2, 4, 5];
        $matches = $this->matchesRepositories->findWhereBetween('status_id', $statusMockIds)->all();
        if ($matches) {
            foreach ($matches as $match) {
                $time = 0;
                switch ($match->status_id) {
                    case 2;
                        $time = rand(1, 44);
                        break;
                    case 4;
                        $time = rand(45, 89);
                        break;
                    case 5;
                        $time = rand(90, 119);
                        break;
                }
                if ($time) {
                    $currentNow = Carbon::now();
                    $newTime = $currentNow->subMinutes($time);
                    $this->matchesRepositories->update([
                        'match_time' => $newTime->timestamp
                    ], $match->id);
                }
            }
        }

        $statusIds = [2, 3, 4, 5, 7];
        $date = Carbon::now()->format('Y-m-d');
        $startOfDay = Carbon::parse($date)->startOfDay()->timestamp;
        $endOfDay = Carbon::parse($date)->endOfDay()->timestamp;

        $data = $this->competitionsRepositories
            ->whereHas('matches', function ($query) use ($statusIds, $startOfDay, $endOfDay) {
                $query->whereBetween('match_time', [$startOfDay, $endOfDay])->whereIn('status_id', $statusIds)->with(['homeTeam', 'awayTeam'])
                    ->orderBy('match_time', 'asc');
            })
            ->with(['matches' => function ($query) use ($statusIds, $startOfDay, $endOfDay) {
                $query->whereBetween('match_time', [$startOfDay, $endOfDay])->whereIn('status_id', $statusIds)->with(['homeTeam', 'awayTeam'])->orderBy('match_time', 'asc');
            }])
            ->all();

        return view('pages.index', compact('data', 'currentNow'));
    }

    public function getMatches(Request $request)
    {
        try {
            $total_matches = 0;
            $mode = $request->mode ?? 'live';

            $statusIds = [2, 3, 4, 5, 7];
            if ($mode == 'end') {
                $statusIds = [8];
            } elseif ($mode == 'schedule') {
                $statusIds = [1];
            } elseif ($mode == 'all') {
                $statusIds = [1, 2, 3, 4, 5, 6, 7, 8, 9];
            }

            $date = Carbon::now()->format('Y-m-d');
            $startOfDay = Carbon::parse($date)->startOfDay()->timestamp;
            $endOfDay = Carbon::parse($date)->endOfDay()->timestamp;
            $currentDateEpochTime = Carbon::now()->timestamp;

            $query = $this->competitionsRepositories
                ->whereHas('matches', function ($query) use ($statusIds, $startOfDay, $endOfDay, $mode, $currentDateEpochTime) {
                    $query = $query->whereIn('status_id', $statusIds)->with(['homeTeam', 'awayTeam'])
                        ->orderBy('match_time', 'asc');

                    if ($mode == 'live') {
                        $query->whereBetween('match_time', [$startOfDay, $endOfDay]);
                    } elseif ($mode == 'schedule') {
                        $query->where('match_time', '>', $currentDateEpochTime);
                    }

                    return $query;
                });
            $matchCount = $query->withCount('matches')->get()->sum('matches_count');

            $data = $query->with(['matches' => function ($query) use ($statusIds, $startOfDay, $endOfDay, $mode, $currentDateEpochTime) {
                $query = $query->whereIn('status_id', $statusIds)->with(['homeTeam', 'awayTeam'])
                    ->orderBy('match_time', 'asc');

                if ($mode == 'live') {
                    $query->whereBetween('match_time', [$startOfDay, $endOfDay]);
                } elseif ($mode == 'schedule') {
                    $query->where('match_time', '>', $currentDateEpochTime);
                }

                return $query;
            }])
                ->get();

            $html = view('render.matches', compact('data', 'mode'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'total_matches' => $matchCount
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json([
                'success' => false,
                'html' => '',
                'total_matches' => 0
            ]);
        }

    }
}
