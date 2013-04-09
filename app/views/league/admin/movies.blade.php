@extends("layout.league")

@section("content")
<h2>Movies</h2>
{{ Former::open()->populate($league) }}
{{ Former::token() }}
	<table class="table">
		<thead>
			<tr>
				<th>Movie</th>
				<th>Bought for</th>
				<th>Player</th>
			</tr>
		</thead>
		<tbody>
			@foreach($league->movies()->orderBy('release', 'asc')->get() as $i => $movie)
				<tr>
					<th>{{{ $movie->name }}}</th>
					<td><div class="input-prepend"><span class="add-on">{{{ $league->units }}}</span>{{ Former::small_text("movies[{$movie->id}][price]")->raw()->value($movie->pivot->price)->pattern('[+-]?\d*\.?\d+') }}</div></td>
					<td>{{ Former::select("movies[{$movie->id}][player]")->raw()->fromQuery($players, "username")->select(isset($movie->users[0]) ? $movie->users[0]->id : 0) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{{ Former::primary_submit("Save") }}
{{ Former::close() }}

@stop