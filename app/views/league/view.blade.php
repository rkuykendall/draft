@extends("layout.league")

@section("content")
<div class="row">
	<div class="span8">
		<h2>Overview</h2>
		<ol>
			@foreach($league->players as $player)
				<li class="player">
					<h3>{{{ $player->displayname }}}</h3>
					<span class="player-money pull-right">Total: ${{{ number_format($player->pivot->earnings_total) }}} <small class="muted">for {{ e($league->units).' '.$player->buytotal.' / '.$league->units.' '.$league->money }}</small></span>
					@if(count($player->movies) > 0)
<?php
$movieNames = $player->movies->map(function($movie) use($league) {
	return e($movie->name).' <span class="muted">('.e($league->units).' '.$movie->lpivot->price.')</span>';
});
echo '<p>Movies: '.implode(', ', $movieNames->toArray()).'</p>';
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
			{{ nl2br(e($league->description)) }}
		</div>
		@if($league->url)
			<p><i class="icon-link"></i> <a href="{{{ $league->url }}}" target="_blank">{{{ $league->url }}}</a></p>
		@endif
		@if(count($league->admins) > 0)
		<small class="muted">Admins:</small>
<?php
$usernames = $league->admins->map(function($user) {
	return e($user->displayname);
});
echo '<p>'.implode(', ', $usernames->toArray()).'</p>';
?>
		@endif
		<h3>League Settings</h3>
		<p><span class="muted">End date:</span> {{{ $league->end_date->toFormattedDateString() }}}</p>
		<p><span class="muted">Buying money:</span> {{{ $league->units.' '.$league->money }}}</p>
		<h3>Movies</h3>
		<ul>
			<?php $now = new DateTime('now'); $printedUpcoming = false; ?>
			@foreach($league->movies as $movie)
				<?php
					if($now < $movie->release && !$printedUpcoming) {
						echo '</ul><div class="upcomingSeparator"><div></div><span>Upcoming</span></div><ul>';
						$printedUpcoming = true;
					}
				?>
				<li>
					{{{ $movie->name }}} <small class="muted">({{{ $now >= $movie->release ? '$'.number_format($movie->latestEarnings->domestic) : $movie->release->toFormattedDateString() }}})</small>
				</li>
			@endforeach
		</ul>
	</div>
</div>
@stop
