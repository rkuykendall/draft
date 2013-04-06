<?php

class League extends Eloquent {

	/* Relationships */

	public function admins() {
		return $this->users()->where("league_user.admin", true);
	}

	public function movies() {
		return $this->belongsToMany('Movie')->withPivot('price')->withTimestamps();
	}

	public function players() {
		return $this->users()->where("league_user.player", true);
	}

	public function users() {
		return $this->belongsToMany('User')->withPivot('player', 'admin')->withTimestamps();
	}




	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	public function getEndDateAttribute($value) {
		return new DateTime($value);
	}
	public function setNameAttribute($value = '') {
		if(!$this->slug) {
			$slug; $i = 0;
			while(true) {
				$slug = Str::limit(Str::slug($value), 255-(strlen($i)+1), "");
				if($i > 0) {
					$slug .= "-".$i;
				}
				if(static::whereSlug($slug)->count() == 0) {
					break;
				}
				$i++;
			}


			$slug = $slug;
			$this->slug = $slug;
		}
		$this->attributes["name"] = $value;
	}


}