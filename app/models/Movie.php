<?php

class Movie extends Eloquent {

	public function getDates() {
		return array_merge(parent::getDates(), array("release"));
	}

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

	public function grabLeaguePivot($lmovie) {
		$this->setRelation("league_pivot", $lmovie->pivot);
		$this->setRelation("latestEarnings", $lmovie->latestEarnings);
	}

}