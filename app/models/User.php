<?php

use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

	/* Relationships */

	public function leagues() {
		return $this->belongsToMany('League')->withPivot('player', 'admin')->withTimestamps();
	}
	public function movies() {
		// Only if called via league
		if(!$this->pivot or $this->pivot->getTable() != "league_user") {
			return $this->belongsToMany('Movie', 'league_movie_user');
		}
		return $this->belongsToMany('Movie', 'league_movie_user')->withPivot('league_id')->where('league_id', $this->pivot->league_id);
	}

	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getDisplaynameAttribute($value) {
		return $value ?: $this->username;
	}

	/* UserInterface */

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	public function getAuthPassword()
	{
		return Hash::make("moz:persona");
	}

}