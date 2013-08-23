<div class="hero-unit">
	<h1>{{{ $league->name }}}</h1>
</div>
<ul class="nav nav-tabs">
	<li{{ Route::currentRouteAction() == "LeagueController@getView" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">Home</a></li>
	<li{{ Route::currentRouteAction() == "LeagueController@getViewPlayers" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getViewPlayers", array("id" => $league->id, "slug" => $league->slug)) }}">Players</a></li>
	<li{{ Route::currentRouteAction() == "LeagueController@getViewMovies" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getViewMovies", array("id" => $league->id, "slug" => $league->slug)) }}">Movies</a></li>

	@if(Auth::check() and $league->userIsAdmin(Auth::user()) and $league->active)
		<li class="dropdown pull-right{{ Request::is('league/*-*/admin/*') ? ' active' : null }}">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li{{ Request::is('league/*-*/admin/settings') ? ' class="active"' : null }}><a href="{{ action("LeagueController@getAdminSettings", array("id" => $league->id, "slug" => $league->slug)) }}">Settings</a></li>
				<li{{ Request::is('league/*-*/admin/users') ? ' class="active"' : null }}><a href="{{ action("LeagueController@getAdminUsers", array("id" => $league->id, "slug" => $league->slug)) }}">Users</a></li>
				<li{{ Request::is('league/*-*/admin/movies') ? ' class="active"' : null }}><a href="{{ action("LeagueController@getAdminMovies", array("id" => $league->id, "slug" => $league->slug)) }}">Movies</a></li>
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