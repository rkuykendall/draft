<?php

use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

	/* Relationships */

	public function leagues() {
		return $this->belongsToMany('League')->withPivot('player', 'admin')->withTimestamps();
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