<?php

class League extends Eloquent {

	public $dates = array("created_at", "updated_at");

	/* Relationships */

	public function admins() {
		return $this->users()->where("league_user.admin", true);
	}

	public function movies() {
		return $this->belongsToMany('Movie')->withPivot('price')->orderBy('release', 'asc')->withTimestamps();
	}

	public function players() {
		return $this->users()->where("league_user.player", true);
	}

	public function users() {
		return $this->belongsToMany('User')->orderBy('earnings_total', 'desc')->orderBy('league_user.id', 'asc')->withPivot('player', 'admin', 'earnings_total')->withTimestamps();
	}

	public function setRelation($relation, $value) {
		parent::setRelation($relation, $value);

		// Listen for users relation, use this to hydrate admins and players
		if($relation == "users") {
			$players = clone $value;
			parent::setRelation("players", $players->filter(function($user) {
				return $user->pivot->player;
			}));
			$admins = clone $value;
			parent::setRelation("admins", $admins->filter(function($user) {
				return $user->pivot->admin;
			}));
		}

	}


	public function userIsAdmin($user) {
		if(isset($this->relations["admins"])) {
			return $this->admins->contains($user->id);
		} else {
			return $this->admins()->whereUser_id($user->id)->count();
		}
	}


	/* Accessors & Mutators (aka. fancy words for getters and setters) */
	// end_date
	public function getEndDateAttribute($value) {
		/* Cannot use $dates due to format */
		if (!($value instanceof DateTime)) {
			return Carbon::createFromFormat('Y-m-d', $value);
		}
		return Carbon::instance($value);
	}
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


}