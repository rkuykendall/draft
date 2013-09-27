@extends("layout.league")

@section("content")
<h2>League Settings</h2>
{{ Former::horizontal_open()->rules($edit_rules)->populate($league) }}
	{{ Former::token() }}
	{{ Former::text("name") }}
	{{ Former::xxlarge_textarea("description") }}
	{{ Former::xxlarge_text("url") }}
	{{ Former::checkbox("private")->value(1)->blockHelp("This won't list the league in public directory. However anyone with the URL can still access it.") }}
	{{ Former::legend("League Settings") }}
	{{ Former::text("money")->value(Config::get("draft.league_defaults.money")) }}
	{{ Former::text("units")->value(Config::get("draft.league_defaults.units")) }}
	{{ Former::select("extra_weeks")->options(array_combine(range(1, $max_weeks), range(1, $max_weeks)))->value(Config::get("draft.league_defaults.extra_weeks"))->label('Extra weeks for earnings')->blockHelp('The amount of weeks a league is going after the last movie has been released.') }}
	<div class="control-group">
		<span class="control-label">League start &amp; end dates</span>
		<div class="controls">
			<p>Currently the league is active {{{ $league->start_date->toFormattedDateString() }}} - {{{ $league->end_date->toFormattedDateString() }}} ({{$league->start_date->diff($league->end_date)->format('%m months %d days') }})</p>
			<p>A league is active from the release of first movie to release of last move + extra weeks. This cannot go over {{ Config::get('draft.max_months') }} months.</p>
		</div>
	</div>
	{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
{{ Former::close() }}
@stop