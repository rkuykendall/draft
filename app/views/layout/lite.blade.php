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

	<!-- Piwik -->
	<script type="text/javascript">
		var _paq = _paq || [];
		_paq.push(['trackPageView']);
		_paq.push(['enableLinkTracking']);
		(function() {
			var u=(("https:" == document.location.protocol) ? "https" : "http") + "://stats.t2t2.eu/";
			_paq.push(['setTrackerUrl', u+'piwik.php']);
			_paq.push(['setSiteId', 1]);
			var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
			g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
		})();

	</script>
	<noscript><p><img src="http://stats.t2t2.eu/piwik.php?idsite=1" style="border:0;" alt="" /></p></noscript>
	<!-- End Piwik Code -->
</body>
</html>
