<?php

class MovieEarning extends Eloquent {

	public $fillable = array("movie_id", "date", "domestic");

	/* Relations */
	public function movie() {
		$this->belongsTo('movie');
	}


}