<?php
class MovieEarning extends Eloquent {
	public $fillable = array("movie_id", "date", "domestic");
	public $dates = array("created_at", "updated_at");

	/* Relations */
	public function movie() {
		$this->belongsTo('movie');
	}

	/* Accessors & Mutators (aka. fancy words for getters and setters) */
	// date
	public function getDateAttribute($value) {
		/* Cannot use $dates due to format */
		if (!($value instanceof DateTime)) {
			return Carbon::createFromFormat('Y-m-d', $value);
		}
		return Carbon::instance($value);
	}

}