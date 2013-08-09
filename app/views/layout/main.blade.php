<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>{{{ isset($title) ? $title.' | ' : null }}}Box Office Draft</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">

	<link href='http://fonts.googleapis.com/css?family=Oxygen:400,700' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="{{ asset("css/bootstrap-combined.font-awesome.min.css") }}">
	<link rel="stylesheet" href="{{ asset("css/main.css") }}">

	<script src="{{ asset("js/vendor/modernizr-2.6.2-respond-1.1.0.min.js") }}"></script>
</head>
<body{{ (isset($javascript[0]) ? " data-controller=\"{$javascript[0]}\"" : null).(isset($javascript[1]) ? " data-action=\"{$javascript[1]}\"" : null).(Auth::check() ? ' data-user="'.Auth::user()->email.'"' : null) }} data-url="{{ url('/') }}" data-csrf="{{ Session::getToken() }}">
	<!--[if lt IE 7]>
		<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
	<![endif]-->

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
											<li{{ Request::is('league/'.$league->id.'-'.$league->slug.'(/*)') ? ' class="active"' : null}}><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">{{{ $league->name }}}</a></li>
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
			<p>Box Office Draft 2013 - <i class="icon-heart-empty"></i> <a href="https://github.com/t2t2/draft" target="_blank">opensource</a> - Powered by unicorns and <a href="http://www.laravel.com/" target="_blank">Laravel 4</a></p>
			<p>Latest Data Update: <span title="{{ $last_update->toCOOKIEString() }}">{{ $last_update->diffForHumans() }}</span></p>
		</footer>

	</div> <!-- /container -->

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="{{ asset("js/vendor/jquery-2.0.2.min.js") }}"><\/script>')</script>
	<script src="https://login.persona.org/include.js"></script>

	<script src="{{ asset("js/vendor/bootstrap.min.js") }}"></script>
	@if(isset($assets) and isset($assets["js"]))
		@foreach($assets["js"] as $url)
			<script src="{{ $url }}"></script>
		@endforeach
	@endif
	<script src="{{ asset("js/main.js") }}"></script>

	<script>
		var _gaq=[['_setAccount','UA-39719686-2'],['_trackPageview']];
		(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
		g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		s.parentNode.insertBefore(g,s)}(document,'script'));
	</script>
</body>
</html>
