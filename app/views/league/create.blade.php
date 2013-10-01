<h1>Create league</h1>
<div class="alert alert-info">
	<h3>Now featuring 2013 Winter Movie Draft!</h3>
</div>
{{ Former::horizontal_open(action("LeagueController@getCreate"))->rules($create_rules) }}
	{{ Former::token() }}
	{{ Former::text("name") }}
	{{ Former::xxlarge_textarea("description") }}
	{{ Former::xxlarge_text("url") }}
	{{ Former::checkbox("private")->blockHelp("This won't list the league in public directory. However anyone with the URL can still access it.") }}
	{{ Former::legend("League Settings") }}
	<p>Note: During alpha you can only copy rules of the NSFWshow Movie Draft..... Well and modify these settings:</p>
	{{ Former::text("money")->value(Config::get("draft.league_defaults.money")) }}
	{{ Former::text("units")->value(Config::get("draft.league_defaults.units")) }}
	{{ Former::select("extra_weeks")->options(array_combine(range(1, 12), range(1, 12)), Config::get("draft.league_defaults.extra_weeks"))->label('Extra weeks for earnings')->blockHelp('The amount of weeks a league is going after the last movie has been released.') }}
	{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
{{ Former::close() }}