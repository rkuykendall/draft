<div class="row">
	<div class="span8">
		<h2>Tracked Leagues</h2>
		<ol>
			@foreach($leagues as $league)
				<li><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">{{{ $league->name }}}</a></li>
			@endforeach
		</ol>
   </div>
	<div class="span4">
		<h1>DISCLAIMER DISCLAIMER DISCLAIMER</h1>
		<p>This is alpha. Really alpha. Like it might break at any second. You've been warned.</p>
	</div>
</div>
