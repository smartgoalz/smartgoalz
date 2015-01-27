<?php
/**
 * The MIT License (MIT)
 *
 * SMARTGoalz - SMART Goals made easier
 *
 * http://smartgoalz.github.io
 *
 * Copyright (c) 2015 Prashant Shah <pshah.smartgoalz@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

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

	public function timewatches()
	{
		return $this->hasMany('Timewatch');
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
