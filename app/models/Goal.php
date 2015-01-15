<?php

class Goal extends Eloquent
{

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
		'is_completed', 'completion_date');

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

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function category()
	{
		return $this->belongsTo('Category');
	}

	public function scopeCurUser($query)
	{
		return $query->where('user_id', '=', Auth::id());
	}
}
