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

	protected $fillable = array('title', 'goal_id', 'start_date', 'due_date', 'is_completed',
		'completion_date', 'notes', 'weight');

	protected $guarded = array('id', 'timewatch_count');

	public function goal()
	{
		return $this->belongsTo('Goal');
	}

	/* Recalculate weights for all task belonging to a goal */
	public static function recalculateWeights($goal_id)
	{
		$step_size = 100000;

		$tasks = Task::where('goal_id', $goal_id)->orderBy('weight', 'ASC')->get();

		$step = $step_size;
		foreach ($tasks as $task) {
			$task->weight = $step;
			$step += $step_size;
			$task->save();
		}
	}

	public function scopeWithGoals($query)
	{
		return $query->leftJoin('goals', 'tasks.goal_id', '=', 'goals.id')
			->where('goals.user_id', '=', Auth::id())
			->select('tasks.*', 'goals.id as goal_id', 'goals.user_id as goals_user_id');
	}
}
