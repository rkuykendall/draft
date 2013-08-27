<h1>Create league</h1>
<div class="alert">
	<h3>Warning!</h3>
	<p>Leagues created right now will be created according to Summer Movie Draft 2013 ruleset. Winter movie draft ruleset has been not yet created.</p>
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
	{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
{{ Former::close() }}