@extends('layout.lite')

@section('section-content')
	<div class="navbar navbar-static-top navbar-inverse">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="{{ url('/') }}">Box Office Draft <sup>&alpha;</sup></a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li{{ Request::is('/') ? ' class="active"' : null}}><a href="{{ url('/') }}">Home</a></li>
						<li class="dropdown{{ Request::is('league/*') ? ' active' : null}}">
							<a href="{{ action("LeagueController@getList") }}" class="dropdown-toggle" data-toggle="dropdown">
								Leagues <b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li{{ Request::is('league/list') ? ' class="active"' : null}}><a href="{{ action("LeagueController@getList") }}">League list</a></li>
								@if(Auth::check())
									<li class="divider"></li>
									<li class="nav-header">My Leagues</li>
									@if(count(Auth::user()->leagues) > 0)
										@foreach(Auth::user()->leagues as $league)
											<li{{ Request::is('league/'.$league->slug.'(/*)') ? ' class="active"' : null}}><a href="{{ action("LeagueController@getView", array("league_slug" => $league->slug)) }}">{{{ $league->name }}}</a></li>
										@endforeach
									@else
										<li class="navbar-text">None</li>
									@endif
									<li class="divider"></li>
									<li{{ Request::is('league/create') ? ' class="active"' : null}}><a href="{{ action("LeagueController@getCreate") }}">New League</a></li>
								@endif
							</ul>
						</li>
					</ul>
					<div class="navbar-text user pull-right">
						@if(Auth::check() and Auth::user()->id)
							<ul class="nav">
								<li class="dropdown">
									<a href="#" id="js-user" class="dropdown-toggle" data-toggle="dropdown">
										Hello {{{ Auth::user()->displayname }}} <b class="caret"></b>
									</a>
									<span id="js-logout-status" class="navbar-text" style="display: none;"></span>
									<ul class="dropdown-menu">
										<li><a href="{{ action("UserController@getView", array("username" => Auth::user()->username)) }}">Profile</a></li>
										<li><a href="#" id="js-logout">Logout</a></li>
									</ul>
								</li>
							</ul>
						@else
							<a id="js-login" class="persona-button dark" href="#"><span>Sign in with your Email</span></a><span id="js-login-status" style="display: none;"></span>
						@endif
					</div>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div id="content" class="container">
		{{ Notification::showAll() }}

		{{ $content }}

		<hr>

		<footer>
			<p>Box Office Draft 2013 - built by <a href="http://t2t2.eu" target="_blank">t2t2</a> - <i class="icon-heart-empty"></i> <a href="https://github.com/t2t2/draft" target="_blank">opensource</a> - Powered by unicorns and <a href="http://www.laravel.com/" target="_blank">Laravel 4</a></p>
			<p>Latest Data Update: <span title="{{ $last_update->toCOOKIEString() }}">{{ $last_update->diffForHumans() }}</span></p>
		</footer>

	</div> <!-- /container -->

@stop