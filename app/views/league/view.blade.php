<h1>{{{ $league->name }}}</h1>
<div class="row">
	<div class="span8">
		<h2>Players</h2>
	</div>
	<div class="span4">
		<small class="muted">About</small>
		<div>
			{{ $league->description }}
		</div>
		<i class="icon-link"></i> <a href="{{{ $league->url }}}">{{{ $league->url }}}</a>
	</div>
</div>