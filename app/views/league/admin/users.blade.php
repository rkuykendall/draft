@extends("layout.league")

@section("content")
<div class="row">
	<div class="span6">
		<h2>Players</h2>
		@if(count($league->players) > 0)
			{{ Former::open() }}
				{{ Former::token() }}
				{{ Former::hidden("action", "remove") }}
				{{ Former::hidden("type", "player")}}
				<ul>
					@foreach($league->players as $user)
						<li>{{{ $user->username }}} <button name="username" value="{{{ $user->username }}}" class="btn btn-mini btn-danger"><i class="icon-remove"></i> Remove</button></li>
					@endforeach
				</ul>
			{{ Former::close() }}
		@else
			<p>No players :'(</p>
		@endif
		<h3>Add player</h3>
		{{ Former::vertical_open()->populate(Input::old("type") == "player" ? Input::old() : array("username" => "")) }}
			{{ Former::token() }}
			{{ Former::hidden("action", "add") }}
			{{ Former::hidden("type", "player") }}
			{{ Former::text("username")->required()->setAttribute('autocomplete', 'off')->data_autocomplete(true)->data_route('users/lookup/')->data_request_data('{"type": "player"}')->forceValue(Input::old("type") == "player" ? Input::old("username") : "") }}
			{{ Former::primary_submit("Add") }}
		{{ Former::close() }}
	</div>
	<div class="span6">
		<h2>Admins</h2>
		@if(count($league->admins) > 0)
			<ul>
				{{ Former::open() }}
					{{ Former::token() }}
					{{ Former::hidden("action", "remove") }}
					{{ Former::hidden("type", "admin")}}
					@foreach($league->admins as $user)
						@if($user->id == Auth::user()->id)
							<li>{{{ $user->username }}} <button disabled class="btn btn-mini btn-danger">It's You!</button></li>
						@else
							<li>{{{ $user->username }}} <button name="username" value="{{{ $user->username }}}" class="btn btn-mini btn-danger"><i class="icon-remove"></i> Remove</button></li>
						@endif
					@endforeach
				{{ Former::close() }}
			</ul>
		@else
			<p>No admin... Hold on, is this a trap? YOU'LL NEVER TAKE ME ALIVE!!!!!</p>
		@endif
		<h3>Add admin</h3>
		{{ Former::vertical_open() }}
			{{ Former::token() }}
			{{ Former::hidden("action", "add") }}
			{{ Former::hidden("type", "admin") }}
			{{ Former::text("username")->required()->setAttribute('autocomplete', 'off')->data_autocomplete(true)->data_route('users/lookup/')->data_request_data('{"type": "admin"}')->forceValue(Input::old("type") == "admin" ? Input::old("username") : "") }}
			{{ Former::primary_submit("Add") }}
		{{ Former::close() }}
	</div>
</div>






@stop