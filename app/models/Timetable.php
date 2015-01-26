<?php

class Timetable extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'timetables';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        protected $hidden = [];

	/**
	 * Model doesnt use timestamps.
	 *
	 * @var string
	 */
	public $timestamps = false;

	protected $fillable = array('from_time', 'to_time', 'days');

	protected $guarded = array('id', 'activity_id');

	public function activity()
	{
		return $this->belongsTo('Activity');
	}

	public function scopeCurUser($query)
	{
		return $query->where('user_id', '=', Auth::id());
	}
}
