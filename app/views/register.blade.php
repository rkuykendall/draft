<h1>Register page!</h1>
<div class="alert">
	<p><strong>Warning!</strong> Loading any other page will get you redirected back to here until you either finish registration or cancel</p>
	<p><a href="#" id="js-register-cancel" class="btn btn-danger">Cancel Registration</a></p>
</div>
<div class="row">
	<div class="span8">
		{{ Former::horizontal_open("register")->rules($register_rules) }}
			{{ Former::token() }}
			{{ Former::medium_text("username") }}
			{{ Former::xlarge_text("displayname", "Display Name") }}
			<div class="control-group">
				<span class="control-label">e-mail</span>
				<div class="controls">
					<span class="uneditable-input">{{ Auth::user()->email }}</span>
					<span class="help-block">Your e-mail will be used for login. It's extremely unlikely that you will recieve e-mail from us.</span>
				</div>
			</div>
			{{ Former::actions()->primary_submit('Submit')->reset('Reset') }}
		{{ Former::close() }}
	</div>
	<div class="span4">
		<h2>Already in a league tracked by us?</h2>
		<p>Due to way databases work we have made a dummy account to track your stats. If you wish to claim ownership of the account, please contact <a href="mailto:{{ Config::get("app.admin_email") }}">site admin</a>.</p>
	</div>
</div>
