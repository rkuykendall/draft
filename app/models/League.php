<?php

class League extends Eloquent {

	public function getDates() {
		return array_merge(parent::getDates(), array("start_date", "end_date"));
	}

	/* Relationships */

	public function admins() {
		return $this->users()->where("league_user.admin", true);
	}

	public function movies() {
		return $this->belongsToMany('Movie')->withPivot('price', 'latest_earnings_id')->orderBy('release', 'asc')->orderBy('movies.id', 'desc')->withTimestamps();
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
			$this->slug = $slug;
		}
		$this->attributes["name"] = $value;
	}

	// League active
	public function getActiveAttribute($value = '') {
		return $this->end_date->isFuture();
	}

	// Update start & end dates
	public function updateLeagueDates() {
		$movies = $this->movies()->get(); // Don't use cached data
		if(count($movies)) {
			$earliest = $movies->first();
			$latest = $movies->last();
			$this->start_date = $earliest->release;
			$maxdate = $this->start_date->copy()->addMonths(Config::get('draft.max_months'));
			$this->end_date = $latest->release->copy()->addWeeks($this->extra_weeks);
			if($this->end_date > $maxdate) {
				$this->end_date = $maxdate;
			}
		} else {
			$this->start_date  = $this->end_date = Carbon::now()->addMonths(Config::get('draft.max_months'));
		}
		$this->save();
	}

	public function maxLastMovieDate() {
		return $this->start_date->copy()->addMonths(Config::get('draft.max_months'))->subWeeks($this->extra_weeks);
	}

	// Maximum extra weeks (6 mtnhs - (latest - earliest))
	public function maxExtraWeeks() {
		$movies = $this->movies()->get();

		if(count($movies)) {
			$first = $movies->first();
			$last = $movies->last();
			$hardlimit = $first->release->addMonths(Config::get('draft.max_months'));
			$maxweeks = floor($hardlimit->diffInDays($last->release) / 7);
		} else {
			$maxweeks = 12;
		}
		$maxweeks = min($maxweeks, 12);
		return $maxweeks;
	}

	/* Query Scope */
	public function scopeActive($query) {
		return $query->where('end_date', '>=', new Carbon('today'));
	}
}