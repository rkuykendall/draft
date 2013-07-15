<div class="row">
	<div class="span8">
		<h2>Featured Leagues</h2>
		<ol>
			@foreach($leagues as $league)
				<li><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">{{{ $league->name }}}</a></li>
			@endforeach
		</ol>
	</div>
	<div class="span4">
		<h2>DISCLAIMER DISCLAIMER DISCLAIMER</h2>
		<p>This is alpha. Really alpha. Like it might break at any second. You've been warned.</p>
		<h2>Create your own league</h2>
		@if(Auth::check())
		<p>Gather up your friends and <a href="{{ action("LeagueController@getCreate") }}">create your own league</a>!</p>
		@else
		<p>First we gotta know who you are! Log in using your e-mail from above!</p>
		<p>Why do we use persona? <a href="https://login.persona.org/about" target="_blank">Cause it's pretty cool.</a></p>
		@endif
	</div>
</div>
