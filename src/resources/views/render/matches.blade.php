<div class="table-responsive">
    @if($data)
        @foreach($data as $item)
            <table class="table">
                <thead>
                <tr>
                    <th colspan="7">
                        <i class="fa fa-star-o" aria-hidden="true"></i>
                        <img src="{{$item->logo}}" class="img-flag">
                        {{$item->name}}
                    </th>
                </tr>
                </thead>
                @if($item->matches && count($item->matches))
                    <tbody>
                    @foreach($item->matches as $match)
                        <tr>
                            <td>
                                <i class="fa fa-star-o" aria-hidden="true"></i>
                            </td>
                            <td>
                                {{formattedTime($match->match_time,  in_array($mode, ['end', 'schedule', 'all']) ? 'Y-m-d H:i:s' : 'H:i')}}
                            </td>
                            <td>
                                {{convertMatchStatus($match->match_time, $match->status_id, $mode)}}
                            </td>
                            <td class="text-right">
                                {{$match->homeTeam->name}}
                                <img src="{{$match->homeTeam->logo}}" class="img-flag">
                            </td>
                            <td class="color-red text-center bold">
                                {{$match->home_scores['0']}} - {{$match->away_scores['0']}}
                            </td>
                            <td class="text-left">
                                <img src="{{$match->awayTeam->logo}}" class="img-flag">
                                {{$match->awayTeam->name}}
                            </td>
                            <td class="item-match-corners">
                                <span>HT</span>
                                <span>{{rand(1,3)}}-{{rand(1,3)}}</span>
                                <span><i class="fa fa-external-link" aria-hidden="true"></i></span>
                                <span>{{rand(1,3)}}-{{rand(1,3)}}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @else
                    <tbody>
                    <tr>
                        <td class="text-center td-not-found" colspan="7">NOT FOUND</td>
                    </tr>
                    </tbody>
                @endif
            </table>
        @endforeach
    @endif
</div>