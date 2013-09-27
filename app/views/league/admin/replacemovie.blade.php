@extends('layout.league')

@section("content")

<div class="row">
	<div class="span8">
		<h2>Replace Movie - {{ $oldmovie->name }}</h2>
	</div>
	<div class="span4"><a href="{{ action("LeagueAdminController@getMovies", array('league_slug' => $league->slug)) }}" class="btn pull-right"><i class="icon-double-angle-left"></i> Back to movies</a></div>
</div>
<p>Search range: {{ $date_range[0]->toFormattedDateString() }} - {{ $date_range[1]->toFormattedDateString() }}</p>

{{ Former::open() }}

@if(count($movies))
	<table class="table">
		<thead>
			<tr>
				<th>Title</th>
				<th>Release Date</th>
				<th class="span2"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($movies as $movie)
				<tr>
					<td>{{ $movie->name }}</td>
					<td>{{ $movie->release->toFormattedDateString() }}</td>
					<th>{{ Form::button('<i class="icon-exchange"></i> Replace', array('class' => 'btn btn-warning', 'name' => 'movie', 'value' => $movie->id, 'type' => 'submit')) }}</th>
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