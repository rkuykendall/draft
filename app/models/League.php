<?php

class League extends Eloquent {

	public function getDates() {
		return array_merge(parent::getDates(), array("end_date"));
	}

	/* Relationships */

	public function admins() {
		return $this->users()->where("league_user.admin", true);
	}

	public function movies() {
		return $this->belongsToMany('Movie')->withPivot('price')->orderBy('release', 'asc')->orderBy('id', 'desc')->withTimestamps();
	}

	public function players() {
		return $this->users()->where("league_user.player", true);
	}

	public function users() {
		return $this->belongsToMany('User')->orderBy('earnings_total', 'desc')->orderBy('league_user.id', 'asc')->withPivot('player', 'admin', 'earnings_total')->withTimestamps();
	}

	public function userIsAdmin($user) {
		if(isset($this->relations["admins"])) {
			return $this->admins->contains($user->id);
		} else {
			return $this->admins()->whereUser_id($user->id)->count();
		}
	}

	/* Accessors & Mutators (aka. fancy words for getters and setters) */

	// name =
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

	// League active
	public function getActive($value = '') {
		return $this->end_date->isFuture();
	}

	/* Query Scope */
	public function scopeActive($query) {
		return $query->where('end_date', '>', Carbon::now());
	}
}