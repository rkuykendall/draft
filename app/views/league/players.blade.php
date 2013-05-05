@extends("layout.league")

@section("content")
<h2>Players</h2>
<div id="earnings-chart" data-source="{{{ action("LeagueController@getChartData", array("id" => $league->id)) }}}"></div>
<ol id="players">
	@foreach($league->players as $player)
		<li class="player">
			<h3>{{{ $player->displayname }}}</h3>
			<span class="player-money pull-right">Total: ${{{ number_format($player->pivot->earnings_total) }}} <small class="muted">for {{ e($league->units).' '.$player->buytotal.' / '.$league->units.' '.$league->money }}</small></span>
			<h4>Movies</h4>
			@if(count($player->movies) > 0)
				<ul class="unstyled">
					@foreach($player->movies as $movie)
						<li>
							<h5>{{{ $movie->name }}} <small>(Release date: {{{ $movie->release->toFormattedDateString() }}})</small></h5>
							<p>Current earnings: {{{ '$'.number_format($movie->latestEarnings->domestic) }}} - Bought for {{{ $league->units.' '.$movie->lpivot->price }}} - {{{ '$'.number_format(round($movie->latestEarnings->domestic/($movie->lpivot->price ?: 1), 2)).' per '.$league->units }}}</p>
						</li>
					@endforeach
				</ul>
			@else
				<p>No movies</p>
			@endif
		</li>
	@endforeach
</ol>
@stop