@extends("layout.league")

@section("content")
<h2>League Settings</h2>
{{ Former::horizontal_open()->rules($edit_rules)->populate($league) }}
	{{ Former::token() }}
	{{ Former::text("name") }}
	{{ Former::xxlarge_textarea("description") }}
	{{ Former::xxlarge_text("url") }}
	{{ Former::legend("League Settings") }}
	{{ Former::text("money")->value(Config::get("draft.league_defaults.money")) }}
	{{ Former::text("units")->value(Config::get("draft.league_defaults.units")) }}
	{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
{{ Former::close() }}
@stop