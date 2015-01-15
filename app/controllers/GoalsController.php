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

use Smartgoalz\Services\Validators\GoalValidator;

class GoalsController extends BaseController
{

	protected $goalValidator;

	public function __construct(GoalValidator $goalValidator)
	{
		$this->goalValidator = $goalValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getIndex()
	{
		$goals = Goal::curUser()->orderBy('title', 'DESC')->get();

		if (!$goals)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'Goals not found.');
		}

		return View::make('goals.index')
			->with('goals', $goals)
			->with('dateformat', $this->dateformat);
	}

	public function getShow($id)
	{
		$goal = Goal::curUser()->find($id);

		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$tasks = Task::where('goal_id', $id)->orderBy('weight', 'ASC')->get();

		return View::make('goals.show')
			->with('goal', $goal)
			->with('tasks', $tasks)
			->with('dateformat', $this->dateformat);
	}

	public function getCreate()
	{
		$categories_list = array('' => 'Please select...') +
			Category::curuser()->orderBy('title', 'ASC')
			->lists('title', 'id');

		return View::make('goals.create')
			->with('categories_list', $categories_list)
			->with('dateformat', $this->dateformat);
	}

	public function postCreate()
	{
		$input = Input::all();

		/* Format start date */
                $start_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' H:i:s',
                        $input['start_date'] . ' 00:00:00'
                );
                if (!$start_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid start date.');
                }
                $start_date = date_format($start_temp, 'Y-m-d H:i:s');

		/* Format due date */
                $due_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' H:i:s',
                        $input['due_date'] . ' 00:00:00'
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

		/* Check for valid category */
		if (!Category::curuser()->find($input['category']))
		{
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid category selected.');
		}

		$this->goalValidator->with($input);

		if ($this->goalValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->goalValidator->getErrors());
		}
		else
		{
			$input['category_id'] = $input['category'];

			/* Set default values for a new goal */
			$input['is_completed'] = 0;
			$input['completion_date'] = NULL;
			$input['task_total'] = 0;
			$input['task_completed'] = 0;
			$input['status'] = 'ACTIVE';

			if (!Goal::create($input))
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create goal.');
			}

                        return Redirect::action('GoalsController@getIndex')
                                ->with('alert-success', 'Congratulations ! Goal created.');
		}
	}

	public function getEdit($id)
	{
		$goal = Goal::curUser()->find($id);

		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$categories_list = array('' => 'Please select...') +
			Category::curuser()->orderBy('title', 'ASC')
			->lists('title', 'id');

		/* Format start date */
                $start_temp = date_create_from_format('Y-m-d H:i:s', $goal->start_date);
                if (!$start_temp)
                {
                        $start_date = '';
                }
		else
		{
			$start_date = date_format($start_temp, explode('|', $this->dateformat)[0]);
		}

		/* Format due date */
                $due_temp = date_create_from_format('Y-m-d H:i:s', $goal->due_date);
                if (!$due_temp)
                {
			$due_date = '';
                }
		else
		{
			$due_date = date_format($due_temp, explode('|', $this->dateformat)[0]);
		}

		return View::make('goals.edit')
			->with('goal', $goal)
			->with('start_date', $start_date)
			->with('due_date', $due_date)
			->with('categories_list', $categories_list)
			->with('dateformat', $this->dateformat);
	}

	public function postEdit($id)
	{
		$goal = Goal::curUser()->find($id);

		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

		$input = Input::all();

		/* Format start date */
                $start_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' H:i:s',
                        $input['start_date'] . ' 00:00:00'
                );
                if (!$start_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid start date.');
                }
                $start_date = date_format($start_temp, 'Y-m-d H:i:s');

		/* Format due date */
                $due_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' H:i:s',
                        $input['due_date'] . ' 00:00:00'
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

		/* Check for valid category */
		if (!Category::curuser()->find($input['category']))
		{
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid category selected.');
		}

		$this->goalValidator->with($input);

		if ($this->goalValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->goalValidator->getErrors());
		}
		else
		{
			/* Update data */
	                $goal->title = $input['title'];
			$goal->category_id = $input['category'];
	                $goal->start_date = $input['start_date'];
	                $goal->due_date = $input['due_date'];
	                $goal->difficulty = $input['difficulty'];
	                $goal->priority = $input['priority'];
	                $goal->reason = $input['reason'];

			if (!$goal->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update goal.');
			}

                        return Redirect::action('GoalsController@getIndex')
                                ->with('alert-success', 'Goal updated.');
		}
	}

	public function deleteDestroy($id)
	{
		$goal = Goal::curUser()->find($id);

		if (!$goal)
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Goal not found.');
		}

                if (!$goal->delete())
		{
			return Redirect::action('GoalsController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete goal.');
		}

		return Redirect::action('GoalsController@getIndex')
			->with('alert-success', 'Goal deleted.');
	}
}
