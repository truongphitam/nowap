<?php

use Carbon\Carbon;

if (!function_exists('formattedTime')) {
    function formattedTime($epochTime, $format = 'Y-m-d H:i:s'): string
    {
        return Carbon::createFromTimestamp($epochTime)->format($format);
    }
}

if (!function_exists('getScores')) {
    function getScores($item, $mode = 'home'): array
    {
        $rand_corners = rand(0, 5);
        $red_cards = rand(1, 3);
        $yellow_cards = rand(1, 6);
        $corners = $rand_corners == 0 ? -1 : $rand_corners;

        $scores = $item['score'] ?? [];

        $homeAway = [
            'fulltime' => $scores['fulltime'][$mode] ?? 0,
            'halftime' => $scores['halftime'][$mode] ?? 0,
            'extratime' => $scores['extratime'][$mode] ?? 0,
            'penalty' => $scores['penalty'][$mode] ?? 0
        ];

        return [
            $homeAway['fulltime'],
            $homeAway['halftime'],
            $red_cards,
            $yellow_cards,
            $corners,
            $homeAway['extratime'],
            $homeAway['penalty']
        ];
    }
}

if (!function_exists('getCompetitions')) {
    function getCompetitions(): array
    {
        return [
            [
                'id' => 15,
                'name' => 'FIFA Club World Cup'
            ],
            [
                'id' => 39,
                'name' => 'Premier League'
            ],
            [
                'id' => 40,
                'name' => 'Championship'
            ],
            [
                'id' => 41,
                'name' => 'League One'
            ],
            [
                'id' => 44,
                'name' => 'FA WSL'
            ],

        ];
    }
}

if (!function_exists('matchStatus')) {
    function matchStatus($id = 0): array
    {

        $status = [
            [
                'id' => 1,
                'status' => 'Not started'
            ],
            [
                'id' => 2,
                'status' => 'First half'
            ],
            [
                'id' => 3,
                'status' => 'Half-time'
            ],
            [
                'id' => 4,
                'status' => 'Second half'
            ],
            [
                'id' => 5,
                'status' => 'Overtime'
            ],
            [
                'id' => 6,
                'status' => 'Overtime(deprecated)'
            ],
            [
                'id' => 7,
                'status' => 'Penalty Shoot-out'
            ],
            [
                'id' => 8,
                'status' => 'End'
            ],
            [
                'id' => 9,
                'status' => 'Delay'
            ]
        ];

        if ($id) {
            foreach ($status as $item) {
                if ($item['id'] == $id) {
                    return $item;
                }
            }
            return [];
        }

        return $status;
    }
}

if (!function_exists('convertMatchStatus')) {
    function convertMatchStatus($match_time, $status_id = 0, $mode = 'live'): string
    {
        $text = '';
        if (in_array($status_id, [2, 4, 5]) && $mode == 'live') {
            $pastTime = Carbon::createFromTimestamp($match_time);
            $now = Carbon::now();
            $differenceInMinutes = $now->diffInMinutes($pastTime);

            $text = $differenceInMinutes . "'";
        }

        if (empty($text) && $status_id) {
            $status = matchStatus($status_id);
            if ($status) {
                $text = __('web.' . $status['status']);
            }

        }

        return $text;
    }
}
