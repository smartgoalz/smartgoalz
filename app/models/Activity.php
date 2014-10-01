<?php

class Activity extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'activities';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        protected $hidden = ['user_id', 'created_at', 'updated_at', 'deleted_at'];

	protected $fillable = array('name');

	protected $guarded = array('id', 'user_id');

	use SoftDeletingTrait;

	public static function boot()
	{
		parent::boot();

		/* Hook into save event, setup event bindings */
		Activity::saving(function($content)
		{
			/* Set user id on save */
			$content->user_id = Auth::id();
		});
	}

	public function scopeWithTimetable($query)
	{
		return $query->rightJoin('timetables', 'activities.id', '=', 'timetables.activity_id')
			->select('timetables.*', 'activities.id as activity_id', 'activities.name as activity_name');
	}

	public function scopeCurUser($query)
	{
		return $query->where('user_id', '=', Auth::id());
	}
}
