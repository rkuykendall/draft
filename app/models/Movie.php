<?php

class Movie extends Eloquent {
	public function users() {
		// Only if called via league
		if(!$this->pivot or $this->pivot->getTable() != "league_movie") {
			return $this->belongsToMany('User', 'league_movie_user');
		}
		return $this->belongsToMany('User', 'league_movie_user')->withPivot('league_id')->where('league_id', $this->pivot->league_id);
	}

	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getReleaseAttribute($value) {
		return new DateTime($value);
	}

	public function grabLeaguePivot($lmovie) {
		$this->setRelation("lpivot", $lmovie->pivot);
	}
}