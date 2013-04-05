<h1>Leagues</h1>

<table class="table table-hover">
	<thead>
		<tr>
			<th>League</th>
		</tr>
	</thead>
	<tbody>
		@foreach($leagues as $league)
			<tr>
				<td>
					<h3><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">
						{{{ $league->name }}}
					</a></h3>
					{{ $league->description }}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>