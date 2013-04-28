<div class="page-header">
	<h1>
		{{{ $user->displayname }}}
		<small>({{{ $user->username }}})</small>
		@if($user->admin)
			<i class="pull-right icon-wrench" title="Draftr Admin"></i>
		@endif
	</h1>
</div>
<h2>User's Leagues</h2>
@if(count($user->leagues) > 0)
	@foreach($user->leagues as $league)
		@if($league->featured)
			<i class="icon-star-empty icon-3x pull-right" title="Featured by Draftr admins"></i>
		@endif
		<h3><a href="{{ action("LeagueController@getView", array("id" => $league->id, "slug" => $league->slug)) }}">
			{{{ $league->name }}}
		</a></h3>
		{{ nl2br(e($league->description)) }}
	@endforeach
@else
	<p>This user has no leagues (s)he's playing in</p>
@endif