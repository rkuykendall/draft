<?php

class League extends Eloquent {

	/* Relationships */

	public function users() {
		return $this->belongsToMany('Users');
	}

	public function players($value='') {
		# code...
	}

}