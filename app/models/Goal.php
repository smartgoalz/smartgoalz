<?php

class Goal extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'goals';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        protected $hidden = ['user_id', 'created_at', 'updated_at', 'deleted_at'];

	protected $fillable = array('category_id', 'title', 'start_date',
		'due_date', 'difficulty', 'priority', 'reason');

	protected $guarded = array('id', 'user_id', 'task_total', 'task_completed',
		'task_reweight', 'is_completed', 'completion_date');

	use SoftDeletingTrait;

	public static function boot()
	{
		parent::boot();

		/* Hook into save event, setup event bindings */
		Goal::saving(function($content)
		{
			/* Set user id on save */
			$content->user_id = Auth::id();
		});
	}

	public function scopeCurUser($query)
	{
		return $query->where('user_id', '=', Auth::id());
	}
}
