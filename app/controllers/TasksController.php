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

use Smartgoalz\Services\Validators\TaskValidator;

class TasksController extends BaseController
{

	public function __construct(TaskValidator $taskValidator)
	{
		$this->taskValidator = $taskValidator;

		$user = User::find(Auth::id());
		$this->dateformat_php = $user->dateformat_php;
		$this->dateformat_cal = $user->dateformat_cal;
		$this->dateformat_js = $user->dateformat_js;
	}

	public function getIndex($goal_id)
	{
		return Redirect::action('GoalsController@getIndex');
	}

	public function getCreate($goal_id)
	{
		$goal = Goal::curUser()->find($goal_id);

		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$tasks_list = array('0' => '(None)') +
			Task::where('goal_id', '=', $goal->id)
			->orderBy('weight', 'ASC')
			->lists('title', 'id');

		return View::make('tasks.create')
			->with('dateformat_cal', $this->dateformat_cal)
			->with('goal', $goal)
			->with('tasks_list', $tasks_list);
	}

	public function postCreate($goal_id)
	{
		$goal = Goal::curUser()->find($goal_id);

		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$input = Input::all();

		/* Check if task is marked as completed */
		if (empty($input['is_completed']))
		{
			$input['is_completed'] = 0;
			$input['completion_date'] = NULL;
		}
		else
		{
			$input['is_completed'] = 1;
		}

		/* Format start date */
		$start_temp = date_create_from_format(
			$this->dateformat_php . ' H:i:s', $input['start_date'] . ' 00:00:00'
		);
		if (!$start_temp)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Invalid start date.');
		}
		$start_date = date_format($start_temp, 'Y-m-d H:i:s');

		/* Format due date */
		$due_temp = date_create_from_format(
			$this->dateformat_php . ' H:i:s', $input['due_date'] . ' 00:00:00'
		);
		if (!$due_temp)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Invalid due date.');
		}
		$due_date = date_format($due_temp, 'Y-m-d H:i:s');

		/* Check if start date if before due date */
		if ($start_temp > $due_temp)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Start date cannot be after due date.');
		}

		$input['start_date'] = $start_date;
		$input['due_date'] = $due_date;

		/* Format completion date */
		if (!is_null($input['completion_date']))
		{
			$completion_temp = date_create_from_format(
				$this->dateformat_php . ' H:i:s', $input['completion_date'] . ' 00:00:00'
			);
			if (!$completion_temp)
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Invalid completion date.');
			}
			$completion_date = date_format($completion_temp, 'Y-m-d H:i:s');

			/* Check if completion date if after start date */
			if ($start_temp > $completion_temp)
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Start date cannot be after completion date.');
			}

			$input['completion_date'] = $completion_date;
		}

		/* Calculate weight for current task */
		if (empty($input['after_id']))
		{
			$input['after_id'] = 0;
		}
		list($weight, $recalculate) = $this->calculateWeight($goal->id, NULL, $input['after_id']);
		unset($input['after_id']);
		$input['weight'] = $weight;

		unset($input['goal_id']);

		$this->taskValidator->with($input);

		if ($this->taskValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->taskValidator->getErrors());
		}
		else
		{
			$task = new Task($input);
			$task->goal()->associate($goal);

			if (!$task->save())
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Failed to create task.');
			}

			/* Recalculate weights of all the tasks if needed */
			if ($recalculate) {
				Task::recalculateWeights($goal->id);
			}

			if (!$this->updateGoalCompletion($goal))
			{
				return Redirect::action('GoalsController@getShow', array($goal->id))
					->with('alert-danger', 'Failed to update goal completion status.');
			}

			return Redirect::action('GoalsController@getShow', array($goal->id))
				->with('alert-success', 'Task added to goal.');

		}
	}

	public function getEdit($id)
	{
		$task = Task::find($id);
		if (!$task)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$tasks_list = array('0' => '(None)') +
			Task::where('goal_id', '=', $goal->id)
			->orderBy('weight', 'ASC')
			->lists('title', 'id');

		/* Format start date */
		$start_temp = date_create_from_format('Y-m-d H:i:s', $task->start_date);
		if (!$start_temp)
		{
			$start_date = '';
		}
		else
		{
			$start_date = date_format($start_temp, $this->dateformat_php);
		}

		/* Format due date */
		$due_temp = date_create_from_format('Y-m-d H:i:s', $task->due_date);
		if (!$due_temp)
		{
			$due_date = '';
		}
		else
		{
			$due_date = date_format($due_temp, $this->dateformat_php);
		}

		/* Format completion date */
		if (!is_null($task->completion_date))
		{
			$completion_temp = date_create_from_format('Y-m-d H:i:s', $task->completion_date);
			if (!$completion_temp)
			{
				$completion_date = '';
			}
			else
			{
				$completion_date = date_format($completion_temp, $this->dateformat_php);
			}
		}
		else
		{
			$completion_date = '';
		}

		return View::make('tasks.edit')
			->with('dateformat_cal', $this->dateformat_cal)
			->with('goal', $goal)
			->with('task', $task)
			->with('start_date', $start_date)
			->with('due_date', $due_date)
			->with('completion_date', $completion_date)
			->with('tasks_list', $tasks_list);
	}

	public function postEdit($id)
	{
		$task = Task::find($id);
		if (!$task)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$input = Input::all();

		/* Check if task is marked as completed */
		if (empty($input['is_completed']))
		{
			$input['is_completed'] = 0;
			$input['completion_date'] = NULL;
		}
		else
		{
			$input['is_completed'] = 1;
		}

		/* Format start date */
		$start_temp = date_create_from_format(
			$this->dateformat_php . ' H:i:s', $input['start_date'] . ' 00:00:00'
		);
		if (!$start_temp)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Invalid start date.');
		}
		$start_date = date_format($start_temp, 'Y-m-d H:i:s');

		/* Format due date */
		$due_temp = date_create_from_format(
			$this->dateformat_php . ' H:i:s', $input['due_date'] . ' 00:00:00'
		);
		if (!$due_temp)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Invalid due date.');
		}
		$due_date = date_format($due_temp, 'Y-m-d H:i:s');

		/* Check if start date if before due date */
		if ($start_temp > $due_temp)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Start date cannot be after due date.');
		}

		$input['start_date'] = $start_date;
		$input['due_date'] = $due_date;

		/* Format completion date */
		if (!is_null($input['completion_date']))
		{
			$completion_temp = date_create_from_format(
				$this->dateformat_php . ' H:i:s', $input['completion_date'] . ' 00:00:00'
			);
			if (!$completion_temp)
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Invalid completion date.');
			}
			$completion_date = date_format($completion_temp, 'Y-m-d H:i:s');

			/* Check if completion date if after start date */
			if ($start_temp > $completion_temp)
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Start date cannot be after completion date.');
			}

			$input['completion_date'] = $completion_date;
		}

		/* Calculate weight for current task */
		if (empty($input['after_id']))
		{
			$input['after_id'] = 0;
		}
		list($weight, $recalculate) = $this->calculateWeight($goal->id, $task->id, $input['after_id']);
		unset($input['after_id']);
		$input['weight'] = $weight;

		unset($input['goal_id']);

		$this->taskValidator->with($input);

		if ($this->taskValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->taskValidator->getErrors());
		}
		else
		{
			$task->title = $input['title'];
			$task->start_date = $input['start_date'];
			$task->due_date = $input['due_date'];
			$task->is_completed = $input['is_completed'];
			$task->completion_date = $input['completion_date'];
			$task->notes = $input['notes'];
			$task->weight = $input['weight'];

			if (!$task->save())
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Failed to update task.');
			}

			/* Recalculate weights of all the tasks if needed */
			if ($recalculate) {
				Task::recalculateWeights($goal->id);
			}

			if (!$this->updateGoalCompletion($goal))
			{
				return Redirect::action('GoalsController@getShow', array($goal->id))
					->with('alert-danger', 'Failed to update goal completion status.');
			}

			return Redirect::action('GoalsController@getShow', array($goal->id))
				->with('alert-success', 'Task updated.');
		}
	}

	public function deleteDestroy($id)
	{
		$task = Task::find($id);
		if (!$task)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$task->timewatches()->delete();

		if (!$task->delete())
		{
			return Redirect::action('GoalsController@getShow', array($goal->id))
				->with('alert-danger', 'Failed to delete task.');
		}

		if (!$this->updateGoalCompletion($goal))
		{
			return Redirect::action('GoalsController@getShow', array($goal->id))
				->with('alert-danger', 'Failed to update goal completion status.');
		}

		return Redirect::action('GoalsController@getShow', array($goal->id))
			->with('alert-success', 'Task deleted.');
	}

	/**
	 * Mark the task as completed
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function postDone($id)
	{
		$task = Task::find($id);
		if (!$task)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$task->is_completed = 1;
		$task->completion_date = date('Y-m-d H:i:s', time());

		if (!$task->save())
		{
			return Redirect::action('GoalsController@getShow', array($goal->id))
				->with('alert-danger', 'Failed to mark task as completed.');
		}

		if (!$this->updateGoalCompletion($goal))
		{
			return Redirect::action('GoalsController@getShow', array($goal->id))
				->with('alert-danger', 'Failed to update goal completion status.');
		}

		return Redirect::action('GoalsController@getShow', array($goal->id))
			->with('alert-success', 'Congratulations ! You have completed a task.');
	}

	/**
	 * Calculate the weight to assign to current task.
	 *
	 * @param  int  $goal_id - Goal id
	 * @param  int  $cur_task_id - Current task id if present else NULL
	 * @param  int  $prev_id - Prev task id if present else 0
	 * @return int, boolean $weight, $recalculate - Calculated weight, If weight recalculation is required
	 */
	public function calculateWeight($goal_id, $cur_task_id, $prev_id = 0)
	{
		$step_size = 100000;
		$recalculate = false;

		$tasks = Task::where('goal_id', $goal_id)->orderBy('weight', 'ASC')->get();
		if (!$tasks)
		{
			return array($step_size, $recalculate);
		}

		/* if edit action, then cur_task_id is set, remove the task from the list */
		if ($cur_task_id) {
			foreach ($tasks as $row => $task) {
				if ($task->id == $cur_task_id) {
					unset($tasks[$row]);
					break;
				}
			}
		}

		/**** Find previous and next task ****/
		$prev_weight = -1;
		$next_weight = -1;

		/* If first task, then prev_weight is 0, calculate next_weight */
		if ($prev_id == 0)
		{
			$prev_weight = 0;
			/* Check if there are any other task after it */
			if (count($tasks) <= 0)
			{
				/* If no other task found then the weight is the step_size */
				return array($step_size, false);
			} else {
				$next_weight = $tasks->first()->weight;
			}
		} else {
			foreach ($tasks as $task) {
				if ($task->id == $prev_id) {
					$prev_weight = $task->weight;
					continue;
				}
				if ($prev_weight != -1) {
					$next_weight = $task->weight;
					break;
				}
			}
			/* If last task, then next_weight will be prev_weight + step_size  */
			if ($next_weight == -1) {
				return array($prev_weight + $step_size, $recalculate);
			}
		}

		/* Calculate weight */
		$inc = ($next_weight - $prev_weight) / 2;
		if ($inc <= 10) {
			/* Re-calculate all tasks weights */
			$recalculate = true;
		}
		$weight = $prev_weight + (($next_weight - $prev_weight) / 2);

		return array($weight, $recalculate);
	}

	/**
	 * Calculate the goal completed status and number of task completed.
	 *
	 * @param object $goal - Goal
	 * @return boolean - Return whether goal was successfully updated with task completion ratios
	 */
	public function updateGoalCompletion($goal)
	{
		/* Initialize counters */
		$count_total = 0;
		$count_completed = 0;
		$count_notcompleted = 0;

		/* Count the number of completed and uncompleted tasks */
		$tasks = Task::where('goal_id', $goal->id)->get();
		foreach ($tasks as $task)
		{
			$count_total++;
			if ($task->is_completed == 1)
			{
				$count_completed++;
			}
			else
			{
				$count_notcompleted++;
			}
		}

		/* Update number of completed and uncompleted tasks in a goal */
		$goal->task_total = $count_total;
		$goal->task_completed = $count_completed;

		/* Check if goal is completed, if all tasks are completed */
		if ($count_total == $count_completed)
		{
			$goal->is_completed = 1;
			$goal->completion_date = date('Y-m-d H:i:s', time());
		}
		else
		{
			$goal->is_completed = 0;
			$goal->completion_date = '0000-00-00 00:00:00';
		}

		/* Update goal */
		if ($goal->save())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
