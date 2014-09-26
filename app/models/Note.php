<?php

class Note extends Eloquent
{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'notes';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
        protected $hidden = ['user_id', 'updated_at', 'deleted_at'];

	protected $fillable = array('title', 'note', 'weight', 'pin_dashboard', 'pin_top');

	protected $guarded = array('id', 'user_id');

	use SoftDeletingTrait;

	// public function user()
	// {
	// 	return $this->belongsTo('User');
	// }

	public static function boot()
	{
		parent::boot();

		/* Hook into save event, setup event bindings */
		Note::saving(function($content)
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
