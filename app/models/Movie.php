<?php

class Movie extends Eloquent {

	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getReleaseAttribute($value) {
		return new DateTime($value);
	}

	public function grabLeaguePivot($lmovie) {
		$this->setRelation("lpivot", $lmovie->pivot);
	}
}