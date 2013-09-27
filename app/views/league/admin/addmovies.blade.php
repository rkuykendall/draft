@extends('layout.league')

@section("content")

<div class="row">
	<div class="span8">
		<h2>Add Movies</h2>
	</div>
	<div class="span4"><a href="{{ action("LeagueAdminController@getMovies", array('league_slug' => $league->slug)) }}" class="btn pull-right"><i class="icon-double-angle-left"></i> Back to movies</a></div>
</div>
<p>Search range: {{ $date_range[0]->toFormattedDateString() }} - {{ $date_range[1]->toFormattedDateString() }}</p>

{{ Former::open() }}

@if(count($movies))
	<table class="table">
		<thead>
			<tr>
				<th class="span1">{{ Former::primary_submit('Add') }}</th>
				<th>Title</th>
				<th>Release Date</th>
			</tr>
		</thead>
		<tbody>
			@foreach($movies as $movie)
				<tr>
					<th>{{ Former::checkbox('movies[]')->value($movie->id)->raw() }}</th>
					<td>{{ $movie->name }}</td>
					<td>{{ $movie->release->toFormattedDateString() }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{{ Former::primary_submit('Add') }}
@else
	<p>No new movies found in the date range</p>
@endif

{{ Former::close() }}

@stop