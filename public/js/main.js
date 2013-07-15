DRAFTR = {
	config: {},
	common: {
		init: function() {
			// Load config
			DRAFTR.config = $.extend({}, {unloading: false}, $(document.body).data())
			// Pants check
			if(location.hostname.indexOf(".dev") > 0) {
				document.title = "[DEV] "+document.title
			}
			// Unload check
			$(window).on("beforeunload", function () {
				DRAFTR.config.unloading = true
			});

			//autocompleters
			$('input[data-autocomplete]').autoComplete();

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
				return false;
			});
			$('#js-logout').click(function () {
				$('#js-user').hide()
				$('#js-logout-status').fadeIn(1000).html('<i class="icon-spinner icon-spin"></i> Logging out...')
				navigator.id.logout();
				return false;
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
	},

	league: {
		players: function () {
			if($("#earnings-chart").length) {
				DRAFTR.league.drawChart($("#earnings-chart"))
			}
		},

		chartData: null,
		drawChart: function ($target) {
			if(!DRAFTR.league.chartData) {
				$target.html('<i class="icon-spinner icon-spin"></i> Loading chart...')
				$.getJSON($target.data("source"), function (data) {
					DRAFTR.league.chartData = data
					DRAFTR.league.drawChart($target)
				});
				return
			}
			$target.html("")
			$target.plot(DRAFTR.league.chartData, {
				legend: {
					position: "nw"
				},
				xaxis: {
					mode: "time",
					//timeformat: "%b %e"
				},
				yaxis: {
					tickFormatter: function (val, axis) {
						return "$"+val.toMoney(axis.tickDecimals)
					}
				},
				series: {
					lines: {
						show: true
					},
					points: {
						show: true
					}
				}
			});
		}
	},

	debounce: function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	}
}; /* DRAFTR */

/* Autocomplete Support */
/* no sanity checking, so use it correctly! */
(function($){
	var $allDropDowns;

	$('body').click(function(){
		if(typeof $allDropDowns != "undefined")
			$allDropDowns.hide();
	});

$.fn.autoComplete = function() {
	$(this).each(function() {
		var requester; //handle on the ajax request
		var $input = $(this);
		var $dropDown = $([
			'<div id="', $input.attr('name'), '-autoComplete" class="dropdown autocomplete">'
				,'<ul class="dropdown-menu"></ul>'
			,'</div>'].join(''));
		$input.after($dropDown.hide());

		typeof $allDropDowns == "undefined" ? $allDropDowns = $dropDown : $allDropDowns.add($dropDown);

		function onload(response) {
			var hasData = false;

			if(response.success) {
				var htmlString = [];
				var users = response.data;
				hasData = response.data.length > 0;

				for(var i = 0, len = users.length; i < len; i++) {
					htmlString.push('<li><a href="javascript:void(\''
						,users[i].username
						,'\');">'
						,users[i].username
						,'</a></li>');
				}

				$dropDown.find('ul').empty().append(htmlString.join(''));
			}

			hasData ? $dropDown.show() : $dropDown.hide();
		}

		$input.keyup(DRAFTR.debounce(function(e){
			if(e.keyCode == 13) { //enter
				$input.closest('form').submit();
			}

			if($input.val().length > 2) {
				if(requester) { requester.abort(); }
				requester = $.ajax({
					url: $input.data('route') + $input.val(),
					data: $input.data('request-data'),
					success: onload
				});
			}
		}, 150));

		$dropDown.delegate('a', 'click', function(){ $input.val($(this).html()); $dropDown.hide(); });
	});
};
})($);

/* http://stackoverflow.com/a/2866613/211088
decimal_sep: character used as deciaml separtor, it defaults to '.' when omitted
thousands_sep: char used as thousands separator, it defaults to ',' when omitted
*/
Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep)
{
   var n = this,
   c = isNaN(decimals) ? 2 : Math.abs(decimals), //if decimal is zero we must take it, it means user does not want to show any decimal
   d = decimal_sep || '.', //if no decimal separator is passed we use the dot as default decimal separator (we MUST use a decimal separator)

   /*
   according to [http://stackoverflow.com/questions/411352/how-best-to-determine-if-an-argument-is-not-sent-to-the-javascript-function]
   the fastest way to check for not defined parameter is to use typeof value === 'undefined'
   rather than doing value === undefined.
   */
   t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, //if you don't want to use a thousands separator you can pass empty string as thousands_sep value

   sign = (n < 0) ? '-' : '',

   //extracting the absolute value of the integer part of the number and converting to string
   i = parseInt(n = Math.abs(n).toFixed(c)) + '',

   j = ((j = i.length) > 3) ? j % 3 : 0;
   return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
}

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