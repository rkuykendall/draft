<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Draftr</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">

		<link href='http://fonts.googleapis.com/css?family=Oxygen:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="{{ asset("css/bootstrap-combined.no-icons.min.css") }}">
		<link rel="stylesheet" href="{{ asset("css/font-awesome.min.css") }}">
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
					<a class="brand" href="{{ url('/') }}">Draftr <sup>&alpha;</sup></a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li{{ Request::is('/') ? ' class="active"' : null}}><a href="{{ url('/') }}">Home</a></li>
						</ul>
						<div class="navbar-text user pull-right">
							@if(Auth::check())
								Hello {{ Auth::user()->displayname }}
							@else
								{{-- <a id="js-login" class="persona-button dark" href="#"><span>Sign in with Persona</span></a><span id="js-login-status" style="display: none;"></span> --}}
							@endif
						</div>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>

		<div class="container">
			{{ $content }}

			<hr>

			<footer>
				<p>Draftr 2013 - <i class="icon-heart-empty"></i> <a href="https://github.com/t2t2/draft" target="_blank">opensource</a> - Powered by unicorns and <a href="http://www.laravel.com/" target="_blank">Laravel 4</a></p>
			</footer>

		</div> <!-- /container -->

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="{{ asset("js/vendor/jquery-1.9.1.min.js") }}"><\/script>')</script>
		<script src="https://login.persona.org/include.js"></script>

		<script src="{{ asset("js/vendor/bootstrap.min.js") }}"></script>

		<script src="{{ asset("js/main.js") }}"></script>

		<!-- <script>
			var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
		</script> -->
	</body>
</html>
