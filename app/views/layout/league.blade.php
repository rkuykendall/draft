<div class="hero-unit">
	<h1>{{{ $league->name }}}</h1>
</div>
<ul class="nav nav-tabs">
	<li{{ Route::currentRouteAction() == "LeagueController@getView" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getView", array("league_slug" => $league->slug)) }}">Home</a></li>
	<li{{ Route::currentRouteAction() == "LeagueController@getViewPlayers" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getViewPlayers", array("league_slug" => $league->slug)) }}">Players</a></li>
	<li{{ Route::currentRouteAction() == "LeagueController@getViewMovies" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getViewMovies", array("league_slug" => $league->slug)) }}">Movies</a></li>
	@if(Auth::check() and $league->userIsAdmin(Auth::user()) and $league->active)
		<li class="dropdown pull-right{{ starts_with(Route::currentRouteAction(), 'LeagueAdminController') ? ' active' : null }}">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li{{ Route::currentRouteAction() == "LeagueAdminController@getAdminSettings" ? ' class="active"' : null }}><a href="{{ action("LeagueAdminController@getAdminSettings", array("league_slug" => $league->slug)) }}">Settings</a></li>
				<li{{ Route::currentRouteAction() == "LeagueAdminController@getAdminUsers" ? ' class="active"' : null }}><a href="{{ action("LeagueAdminController@getAdminUsers", array("league_slug" => $league->slug)) }}">Users</a></li>
				<li{{ Route::currentRouteAction() == "LeagueAdminController@getAdminMovies" ? ' class="active"' : null }}><a href="{{ action("LeagueAdminController@getAdminMovies", array("league_slug" => $league->slug)) }}">Movies</a></li>
			</ul>
		</li>
	@endif
</ul>

@if(!$league->active)
<div class="alert alert-info">
	This league ended on {{ $league->end_date->toFormattedDateString() }}
</div>
@endif

@yield("content")