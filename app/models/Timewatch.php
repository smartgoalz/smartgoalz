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

	use SoftDeletingTrait;

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
