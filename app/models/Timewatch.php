<?php

class Timewatch extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'timewatches';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        protected $hidden = ['user_id', 'created_at', 'updated_at'];

	protected $fillable = array('task_id', 'start_time', 'stop_time', 'is_active');

	protected $guarded = array('id', 'user_id', 'date', 'minutes_count');

	public static function boot()
	{
		parent::boot();

		/* Hook into save event, setup event bindings */
		Timewatch::saving(function($content)
		{
			/* Set user id on save */
			$content->user_id = Auth::id();
		});
	}

	public function scopeCurUser($query)
	{
		return $query->where('user_id', '=', Auth::id());
	}

	public function scopeWithTasks($query)
	{
		return $query->leftJoin('tasks', 'timewatches.task_id', '=', 'tasks.id')
			->select('timewatches.*', 'tasks.title as tasks_title');
	}
}
