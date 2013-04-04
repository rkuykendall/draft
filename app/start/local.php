<?php

App::missing(function($exception) {
	$layout = View::make("layout.main");
	$layout->content = View::make("errors.404");
	return Response::make($layout, 404);
});

View::composer("layout.main", function ($view) {
	if(!isset($view->javascript)) {
		$view->javascript = array();
	}
	if(!isset($view->content)) {
		$view->content = "";
	}
});