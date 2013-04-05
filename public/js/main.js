DRAFTR = {
	config: {},
	common: {
		init: function() {
			// Load config
			DRAFTR.config = $.extend({}, {unloading: false}, $(document.body).data())
			// Pants check
			if(location.hostname.endsWith(".dev")) {
				document.title = "[DEV] "+document.title
			}
			// Unload check
			$(window).on("beforeunload", function () {
				DRAFTR.config.unloading = true
			});
			// Persona
			navigator.id.watch({
				loggedInUser: (DRAFTR.config.user ? DRAFTR.config.user : null),
				onlogin: function (assertion) {
					if($('#js-login:hidden').length == 0) {
						$('#js-login').hide()
						$('#js-login-status').fadeIn(200)
					}
					$('#js-login-status').html('<i class="icon-spinner icon-spin"></i> Logging in...')
					$.ajax({
						type: 'POST',
						url: DRAFTR.config.url+'/login',
						data: {
							"_token": DRAFTR.config.csrf,
							"assertion": assertion
						},
						success: function (response) {
							if(response.refresh) {
								window.location.reload()
							}
							if(response.redirect) {
								window.location = response.redirect
							}
						},
						error: function () {
							navigator.id.logout();
						}
					});
				},
				onlogout: function () {
					setTimeout(function() { // http://stackoverflow.com/a/15623312/211088
						if(DRAFTR.config.unloading) {
							return false;
						}

						$.ajax({
							type: 'POST',
							url: DRAFTR.config.url+'/logout',
							data: {
								"_token": DRAFTR.config.csrf
							},
							success: function (response) {
								if(response.refresh) {
									window.location.reload()
								}
								if(response.redirect) {
									window.location = response.redirect
								}
							},
							error: function () {
								window.location.reload()
							}
						});
					}, 100);
				}
			});
			// Login
			$('#js-login').click(function () {
				$('#js-login').hide()
				$('#js-login-status').fadeIn(1000).html('<i class="icon-spinner icon-spin"></i> Waiting on user...')
				navigator.id.request({
					siteName: 'Draftr',
					oncancel: function () {
						$('#js-login-status').html("Cancelled by user")
						setTimeout(function function_name (argument) {
							$('#js-login-status').hide()
							$('#js-login').fadeIn(500)
						}, 2000);
					}
				});
				return;
			});
			$('#js-logout').click(function () {
				$('#js-user').hide()
				$('#js-logout-status').fadeIn(1000).html('<i class="icon-spinner icon-spin"></i> Logging out...')
				navigator.id.logout();
				return;
			});
		}
	},

	register: {
		init: function() {
			// Psst.... Registration cancel is just fancy logout
			$("#js-register-cancel").one("click", function () {
				$(this).addClass("disabled")
				$(this).html('<i class="icon-spinner icon-spin"></i> '+$(this).html())
				navigator.id.logout()
			}).on("click", function () {
				return false;
			});
		}
	}
}; /* DRAFTR */

/* http://viget.com/inspire/extending-paul-irishs-comprehensive-dom-ready-execution */
UTIL = {
	exec: function( controller, action ) {
		var ns = DRAFTR,
		    action = ( action === undefined ) ? "init" : action;

		if (controller !== "" && ns[controller] && typeof ns[controller][action] == "function") {
			ns[controller][action]();
		}
	},

	init: function() {
		var body = document.body,
		    controller = body.getAttribute("data-controller"),
		    action = body.getAttribute("data-action");

		UTIL.exec("common");
		UTIL.exec(controller);
		UTIL.exec(controller, action);
	}
};
$(document).ready(UTIL.init);