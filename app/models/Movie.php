<?php

class Movie extends Eloquent {

	public $dates = array("created_at", "updated_at");

	/* Relations */
	public function users() {
		// Only if called via league
		if(!$this->pivot or $this->pivot->getTable() != "league_movie") {
			return $this->belongsToMany('User', 'league_movie_user')->withPivot('league_id');
		}
		return $this->belongsToMany('User', 'league_movie_user')->withPivot('league_id')->where('league_id', $this->pivot->league_id);
	}
	public function earnings() {
		return $this->hasMany('MovieEarning');
	}
	public function latestEarnings() {
		return $this->belongsTo('MovieEarning');
	}

	/* Accessors & Mutators (aka. fancy words for getters and setters) */
	// release
	public function getReleaseAttribute($value) {
		/* Cannot use $dates due to format */
		if (!($value instanceof DateTime)) {
			return Carbon::createFromFormat('Y-m-d', $value);
		}
		return Carbon::instance($value);
	}

	public function grabLeaguePivot($lmovie) {
		$this->setRelation("lpivot", $lmovie->pivot);
		$this->setRelation("latestEarnings", $lmovie->latestEarnings);
	}
}