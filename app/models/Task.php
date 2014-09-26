<?php

class Task extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tasks';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        protected $hidden = ['goal_id', 'updated_at', 'deleted_at'];

	protected $fillable = array('title', 'start_date', 'due_date', 'is_completed',
		'completion_date', 'reminder_time', 'notes');

	protected $guarded = array('id', 'goal_id');

}
