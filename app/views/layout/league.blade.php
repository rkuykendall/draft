<div class="hero-unit">
	<h1>{{{ $league->name }}}</h1>
</div>
<ul class="nav nav-tabs">
	<li{{ Route::currentRouteAction() == "LeagueController@getView" ? ' class="active"' : null }}><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">Home</a></li>

	@if($league->userIsAdmin(Auth::user()))
		<li class="dropdown pull-right">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">Admin <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="#">Settings</a></li>
				<li><a href="{{ action("LeagueController@getAdminUsers", array("id" => $league->id, "slug" => $league->slug)) }}">Users</a></li>
				<li><a href="#">Movies</a></li>
			</ul>
		</li>
	@endif
</ul>

@yield("content")