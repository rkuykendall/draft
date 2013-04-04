<div class="hero-unit">
	<h1>{{{ $league->name }}}</h1>
</div>
<ul class="nav nav-tabs">
	<li{{ Route::currentRouteAction() == "LeagueController@getView" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">Home</a></li>
</ul>
<div class="row">
	<div class="span8">
		<h2>Players</h2>
		<ol>
			@foreach($league->players as $player)
				<li class="player">
					<h3>{{{ $player->displayname }}}</h3>
					<span class="player-money">Total: $0 <sup class="muted">for {{ e($league->units).' '.$player->buytotal }}</sup></span>
					@if(count($player->movies) > 0)
<?php
$movieNames = $player->movies->map(function($movie) use($league) {
	return e($movie->name).' <span class="muted">('.e($league->units).' '.$movie->lpivot->price.')</span>';
});
echo '<p>Movies: '.implode(', ', $movieNames).'</p>';
?>
					@else
						<p>Movies: None</p>
					@endif
				</li>
			@endforeach
		</ol>
	</div>
	<div class="span4">
		<small class="muted">About</small>
		<div>
			{{ $league->description }}
		</div>
		<p><i class="icon-link"></i> <a href="{{{ $league->url }}}" target="_blank">{{{ $league->url }}}</a></p>
		<h3>League Settings</h3>
		<p><span class="muted">End date:</span> {{{ $league->end_date->format("F j, Y") }}}</p>
		<h3>Movies</h3>
		<ul>
			@foreach($league->movies as $movie)
				<li>
					{{{ $movie->name }}} <small class="muted">({{{ $movie->release->format("F j, Y") }}})</small>
				</li>
			@endforeach
		</ul>
	</div>
</div>