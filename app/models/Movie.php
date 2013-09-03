<?php

class Movie extends Eloquent {

	public function getDates() {
		return array_merge(parent::getDates(), array("release"));
	}

	/* Relations */
	public function users() {
		// If called $league->movie->users, only get the user for this movie
		return $this->belongsToMany('User', 'league_movie_user')->withPivot('league_id');
	}
	public function earnings() {
		return $this->hasMany('MovieEarning');
	}
	public function latestEarnings() {
		return $this->belongsTo('MovieEarning');
	}
	public function getLatestEarningsIdAttribute($value) {
		// If loaded by league->movie, get the reccomended latest_earnings_id from the pivot
		if($this->pivot and $this->pivot->getTable() == "league_movie" and !is_null($this->pivot->latest_earnings_id)) {
			return $this->pivot->latest_earnings_id;
		}
		return $value;
	}
	public function getLatestEarningsAttribute($value) {
		// Manual relation loading to allow for defaults
		if(!isset($this->relations['latestEarnings'])) {
			$this->relations['latestEarnings'] = $this->latestEarnings()->getResults();
		}
		return $this->relations['latestEarnings'] ?: new MovieEarning(array('domestic' => 0));
	}

	public function grabLeaguePivot($lmovie) {
		$this->setRelation("league_pivot", $lmovie->getRelation('pivot'));
		$this->setRelation("latestEarnings", $lmovie->getRelation('latestEarnings'));
	}

}