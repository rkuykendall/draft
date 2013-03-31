<?php

use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

	/* Relationships */

	public function leagues() {
		return $this->belongsToMany('League')->withPivot('player', 'admin')->withTimestamps();
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
		return null;
	}

}