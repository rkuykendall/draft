<?php

class Movie extends Eloquent {

	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getReleaseAttribute($value) {
		return new DateTime($value);
	}

}