@extends("layout.league")

@section("content")
<h2>League Settings</h2>
{{ Former::horizontal_open()->rules($edit_rules)->populate($league) }}
	{{ Former::token() }}
	{{ Former::text("name") }}
	{{ Former::xxlarge_textarea("description") }}
	{{ Former::xxlarge_text("url") }}
	{{ Former::checkbox("private")->blockHelp("This won't list the league in public directory. However anyone with the URL can still access it.") }}
	{{ Former::legend("League Settings") }}
	{{ Former::text("money")->value(Config::get("draft.league_defaults.money")) }}
	{{ Former::text("units")->value(Config::get("draft.league_defaults.units")) }}
	{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
{{ Former::close() }}
@stop