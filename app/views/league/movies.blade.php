@extends("layout.league")

@section("content")
<h2>Movies</h2>
@if(count($league->players) > 0)
	<table class="table">
		<thead>
			<th>Name</th>
			<th>Release Date</th>
			<th><abbr title="Box Office Mojo">BOM</abbr> link</th>
			<th>Domestic Gross</th>
			<th>Owner</th>
			<th>Purchase Price</th>
			<th>Gross / Purchase Price</th>
		</thead>
		<tbody>
			@foreach($league->movies as $movie)
<?php
$bom_url = 'http://boxofficemojo.com/movies/?id='.$movie->boxmojo_id.'.htm';
?>
				<tr>
					<td>{{{ $movie->name }}}</td>
					<td>{{{ $movie->release->toFormattedDateString() }}}</td>
					<td><small><a href="{{ $bom_url }}" target="_blank"><i class="icon-share"></i> Go</a></small></td>
					<td>{{{ '$'.number_format($movie->latestEarnings->domestic) }}}</td>
					@if($user = $movie->users->first())
					<td>{{{ $user->displayname }}}</td>
					<td>{{{ $league->units.' '.$movie->pivot->price }}}</td>
					<td>{{{ '$'.number_format(round($movie->latestEarnings->domestic/($movie->pivot->price ?: 1), 2)).' per '.$league->units }}}</td>
					@else
					<td colspan="3">Nobody</td>
					@endif
				</tr>
			@endforeach		
		</tbody>
	</table>
@else
	<p>This league currently uses no movies :(</p>
@endif
@stop