@extends("layout.league")

@section("content")
<div class="row">
	<div class="span6">
		<h2>Players</h2>
		@if(count($league->players) > 0)
			<ul>
				@foreach($league->players as $user)
					<li>{{{ $user->username }}}</li>
				@endforeach
			</ul>
		@else
			<p>No players :'(</p>
		@endif
		<h3>Add player</h3>
		{{ Former::vertical_open()->populate(Input::old("type") == "player" ? Input::old() : array("username" => "")) }}
			{{ Former::token() }}
			{{ Former::hidden("action", "add") }}
			{{ Former::hidden("type", "player") }}
			{{ Former::text("username")->required()->forceValue(Input::old("type") == "player" ? Input::old("username") : "") }}
			{{ Former::primary_submit("Add") }}
		{{ Former::close() }}
	</div>
	<div class="span6">
		<h2>Admins</h2>
		@if(count($league->admins) > 0)
			<ul>
				@foreach($league->admins as $user)
					<li>{{{ $user->username }}}</li>
				@endforeach
			</ul>
		@else
			<p>No admins :'(</p>
		@endif
		<h3>Add admin</h3>
		{{ Former::vertical_open() }}
			{{ Former::token() }}
			{{ Former::hidden("action", "add") }}
			{{ Former::hidden("type", "admin") }}
			{{ Former::text("username")->required()->forceValue(Input::old("type") == "admin" ? Input::old("username") : "") }}
			{{ Former::primary_submit("Add") }}
		{{ Former::close() }}
	</div>
</div>






@stop