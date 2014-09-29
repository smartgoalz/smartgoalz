<?php

class Monitorvalue extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'monitorvalues';

	/**
	 * Model doesnt use timestamps.
	 *
	 * @var string
	 */
	public $timestamps = false;

	protected $fillable = array('monitor_id', 'value', 'date');

	protected $guarded = array('id');

}
