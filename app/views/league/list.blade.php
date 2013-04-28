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
					@if($league->featured)
						<i class="icon-star-empty icon-3x pull-right" title="Featured by Draftr admins"></i>
					@endif
					<h3><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">
						{{{ $league->name }}}
					</a></h3>
					{{ nl2br(e($league->description)) }}
				</td>
			</tr>
		@endforeach
	</tbody>
</table>