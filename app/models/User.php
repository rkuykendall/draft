<?php

use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

	protected $hidden = array('email');

	/* Relationships */

	public function leagues() {
		return $this->belongsToMany('League')->withPivot('player', 'admin')->withTimestamps();
	}
	public function movies() {
		$relationship = $this->belongsToMany('Movie', 'league_movie_user')->withPivot('league_id')->withTimestamps();
		// If called via league->user>movies only get movies related to the league
		if($this->pivot and $this->pivot->getTable() == "league_user") {
			return $relationship->where('league_id', $this->pivot->league_id)->orderBy('release', 'asc');
		}
		return $relationship;
	}


	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getDisplaynameAttribute($value) {
		return $value ?: $this->username;
	}

	/* UserInterface */
	public function getAuthIdentifier() {
		return $this->getKey();
	}

	public function getAuthPassword() {
		return Hash::make("moz:persona");
	}

}