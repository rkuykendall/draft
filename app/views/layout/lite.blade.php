<!DOCTYPE html>
<!--[if lt IE 8]>      <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
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
<body{{ $jsdata }}>
	<!--[if lt IE 7]>
		<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
	<![endif]-->

	@section('section-content')

	{{ $content }}

	@show

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
