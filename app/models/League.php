<?php

class League extends Eloquent {

	/* Relationships */

	public function admins() {
		return $this->users()->where("admin", true);
	}

	public function movies() {
		return $this->belongsToMany('Movie')->withPivot('price')->withTimestamps();
	}

	public function players() {
		return $this->users()->where("player", true);
	}

	public function users() {
		return $this->belongsToMany('User')->withPivot('player', 'admin')->withTimestamps();
	}




	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getEndDateAttribute($value) {
		return new DateTime($value);
	}


}