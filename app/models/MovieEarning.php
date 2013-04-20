<?php
class MovieEarning extends Eloquent {
	public $fillable = array("movie_id", "date", "domestic");

	/* Relations */
	public function movie() {
		$this->belongsTo('movie');
	}

	/* Accessors & Mutators (aka. fancy words for getters and setters) */
	public function getDateAttribute($value) {
		if(is_string($value))
			return new DateTime($value);
		return $value;
	}
	public function getCreatedAtAttribute($value) {
		if(is_string($value))
			return new DateTime($value);
		return $value;
	}
	public function getUpdatedAtAttribute($value) {
		if(is_string($value))
			return new DateTime($value);
		return $value;
	}


}