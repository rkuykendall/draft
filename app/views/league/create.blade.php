<h1>Create league</h1>
{{ Former::horizontal_open(action("LeagueController@getCreate"))->rules($create_rules) }}
	{{ Former::token() }}
	{{ Former::text("name") }}
	{{ Former::xxlarge_textarea("description") }}
	{{ Former::xxlarge_text("url") }}
	{{ Former::legend("League Settings") }}
	<p>Note: During alpha you can only copy rules of the NSFWshow Movie Draft..... Well and modify these settings:</p>
	{{ Former::text("money")->value(Config::get("draft.league_defaults.money")) }}
	{{ Former::text("units")->value(Config::get("draft.league_defaults.units")) }}
	{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
{{ Former::close() }}