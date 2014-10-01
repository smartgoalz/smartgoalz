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

	protected $fillable = array('activity_id', 'from_time', 'to_time', 'days');

	protected $guarded = array('id');

}
