@extends("layout.league")

@section("content")
<div class="row">
	<div class="span8">
		<h2>Movies</h2>
	</div>
	<div class="span4"><a href="{{ action("LeagueAdminController@getAddMovies", array('league_slug' => $league->slug)) }}" class="btn pull-right"><i class="icon-plus"></i> Add Movies</a></div>
</div>
{{ Former::open()->populate($league) }}
{{ Former::token() }}
	<table class="table">
		<thead>
			<tr>
				<th>Movie</th>
				<th>Bought for</th>
				<th>Player</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			@if(count($league->movies) > 0)
				@foreach($league->movies as $i => $movie)
					<tr>
						<th>{{{ $movie->name }}}<br /><small class="muted">Release date: {{ $movie->release->toFormattedDateString() }}</small></th>
						<td><div class="input-prepend"><span class="add-on">{{{ $league->units }}}</span>{{ Former::small_text("movies[{$movie->id}][price]")->raw()->value($movie->pivot->price)->pattern('[+-]?\d*\.?\d+') }}</div></td>
						<td>{{ Former::select("movies[{$movie->id}][player]")->raw()->fromQuery($players, "username")->select(isset($movie->users[0]) ? $movie->users[0]->id : 0) }}</td>
						<td>
							<div class="btn-group">
								@if(!$movie->released)
									<a href="{{ action('LeagueAdminController@getMoviesReplace', array('league_slug' => $league->slug, 'movie' => $movie->id)) }}" class="btn btn-warning" title="Swap this movie"><i class="icon-exchange"></i></a>
									{{ Form::button('<i class="icon-remove"></i>', array('class' => 'btn btn-danger', 'name' => 'remove', 'value' => $movie->id, 'type' => 'submit')) }}
								@else
									<div class="btn disabled btn-warning" title="Swapping movies after release is disabled"><i class="icon-exchange"></i></div>
									<div class="btn disabled btn-danger" title="Removing movies after release is disabled"><i class="icon-remove"></i></div>
								@endif
							</div>

						</td>
					</tr>
				@endforeach
			@else
				<tr>
					<th colspan="4">This league doesn't currently use any movies.</th>
				</tr>
			@endif
		</tbody>
	</table>
	{{ Former::primary_submit("Save") }}
{{ Former::close() }}

@stop