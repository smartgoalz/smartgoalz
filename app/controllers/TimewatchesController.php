<?php

use Smartgoalz\Services\Validators\TimewatchValidator;

class TimewatchesController extends BaseController
{

	protected $timewatchValidator;

	protected $timewatchStopValidator;

	public function __construct(TimewatchValidator $timewatchValidator)
	{
		$this->timewatchValidator = $timewatchValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getIndex()
	{
		return Redirect::action('TimewatchesController@getStart');
	}

	public function getShow($id)
	{
		$timewatch = Timewatch::curUser()->find($id);
		if (!$timewatch)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Timewatch not found.');
		}

		$task = Task::find($timewatch->task_id);
		if (!$task)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Goal not found.');
		}

		return View::make('timewatches.show')
			->with('goal', $goal)
			->with('task', $task)
			->with('timewatch', $timewatch)
			->with('dateformat', $this->dateformat);
	}

	public function getStart()
	{
		$goals = Goal::curUser()->orderBy('title', 'DESC')->get();

		$timewatches = Timewatch::curUser()->withTasks()
			->where('is_active', 0)
			->orderBy('date', 'DESC')
			->paginate(20);

		$timewatches_active = Timewatch::curUser()->withTasks()
			->where('is_active', 1)
			->orderBy('date', 'DESC')
			->get();

		return View::make('timewatches.start')
			->with('timewatches', $timewatches)
			->with('timewatches_active', $timewatches_active)
			->with('goals', $goals)
			->with('dateformat', $this->dateformat);
	}

	public function postStart()
	{
		$input = Input::all();

		$task = Task::find($input['task_id']);
		if (!$task)
		{
                        return Redirect::back()->withInput()
				->with('alert-danger', 'Please select a task.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
                        return Redirect::back()->withInput()
				->with('alert-danger', 'Goal not found.');
		}

		$start_time = date('Y-m-d H:i:s', time());

		/* Convert to php time */
		$start_ts = strtotime($start_time);

		$input['start_time'] = $start_time;
		$input['stop_time'] = NULL;
		$input['is_active'] = 1;

		$this->timewatchValidator->with($input);

		if ($this->timewatchValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->timewatchValidator->getErrors());
		}
		else
		{
			$timewatch = Timewatch::create($input);
			if (!$timewatch)
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create timewatch.');
			}

			/* Update timewatch date */
			$timewatch->date = date('Y-m-d', $start_ts);
			$timewatch->save();

                        return Redirect::action('TimewatchesController@getStop', array($timewatch->id))
                                ->with('alert-success', 'Timewatch created.');
		}
	}

	public function getStop($id)
	{
		$timewatch = Timewatch::curUser()->find($id);
		if (!$timewatch)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Timewatch not found.');
		}

		if ($timewatch->is_active == 0)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Timewatch already stopped.');
		}

		$task = Task::find($timewatch->task_id);
		if (!$task)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Goal not found.');
		}

		return View::make('timewatches.stop')
			->with('goal', $goal)
			->with('task', $task)
			->with('timewatch', $timewatch)
			->with('dateformat', $this->dateformat);
	}

	public function postStop($id)
	{
		$timewatch = Timewatch::curUser()->find($id);
		if (!$timewatch)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Timewatch not found.');
		}

		if ($timewatch->is_active == 0)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Timewatch already stopped.');
		}

		$task = Task::find($timewatch->task_id);
		if (!$task)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Task not found.');
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Goal not found.');
		}

		$start_time = $timewatch->start_time;
		$stop_time = date('Y-m-d H:i:s', time());

		/* Convert to php time */
		$start_ts = strtotime($start_time);
		$stop_ts = strtotime($stop_time);

		/* Validate start and stop time */
		if ($start_ts > $stop_ts)
		{
		        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Stop time cannot be before start time.');
		}

		/* Calculate difference between stop and start time in minutes */
		$minutes_count = abs($stop_ts - $start_ts) / 60;
		$minutes_count = round($minutes_count, 0);

		$timewatch->stop_time = $stop_time;
		$timewatch->is_active = 0;
		$timewatch->minutes_count = $minutes_count;

		if (!$timewatch->save())
		{
		        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Failed to stop timewatch.');
		}

                return Redirect::action('TimewatchesController@getStart')
                        ->with('alert-success', 'Timewatch stopped.');
	}

	public function getEdit($id = null)
	{
		$timewatch = null;
		$task = null;
		$start_time = '';
		$stop_time = '';
		$active = TRUE;

		if ($id)
		{
			$timewatch = Timewatch::curUser()->find($id);
			if (!$timewatch)
			{
				return Redirect::action('TimewatchesController@getStart')
					->with('alert-danger', 'Timewatch not found.');
			}

			$task = Task::find($timewatch->task_id);
			if (!$task)
			{
				return Redirect::action('TimewatchesController@getStart')
					->with('alert-danger', 'Task not found.');
			}

			$goal = Goal::curUser()->find($task->goal_id);
			if (!$goal)
			{
				return Redirect::action('TimewatchesController@getStart')
					->with('alert-danger', 'Goal not found.');
			}

			/* Format start time */
	                $start_temp = date_create_from_format('Y-m-d H:i:s', $timewatch->start_time);
	                if (!$start_temp)
	                {
	                        $start_time = '';
	                }
			else
			{
				$start_time = date_format($start_temp, explode('|', $this->dateformat)[0] . ' h:i A');
			}

			/* Format stop time */
	                $stop_temp = date_create_from_format('Y-m-d H:i:s', $timewatch->stop_time);
	                if (!$stop_temp)
	                {
				$stop_time = '';
	                }
			else
			{
				$stop_time = date_format($stop_temp, explode('|', $this->dateformat)[0] . ' h:i A');
			}

			if ($timewatch->is_active == 1)
			{
				$active = TRUE;
				$stop_time = '';
			}
			else
			{
				$active = FALSE;
			}
		}

		$goals = Goal::curUser()->orderBy('title', 'DESC')->get();

		return View::make('timewatches.edit')
			->with('goals', $goals)
			->with('timewatch',  $timewatch)
			->with('timewatch_task', $task)
			->with('start_time', $start_time)
			->with('stop_time', $stop_time)
			->with('active', $active)
			->with('dateformat', $this->dateformat);
	}

	public function postEdit($id = null)
	{
		$input = Input::all();

		/* Check if timewatch is stopped */
		if (empty($input['is_active']))
		{
			$input['is_active'] = 0;
		}
		else
		{
			$input['is_active'] = 1;
		}

		/* Format start time */
                $start_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' h:i A',
                        $input['start_time']
                );
                if (!$start_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid start time.');
                }
                $start_time = date_format($start_temp, 'Y-m-d H:i:s');

		/* Convert to php time */
		$start_ts = strtotime($start_time);

		$minutes_count = 0;
		if ($input['is_active'] == 0)
		{
			/* Format stop time */
	                $stop_temp = date_create_from_format(
	                        explode('|', $this->dateformat)[0] . ' h:i A',
	                        $input['stop_time']
	                );
	                if (!$stop_temp)
	                {
	                        return Redirect::back()->withInput()
	                                ->with('alert-danger', 'Invalid stop time.');
	                }
	                $stop_time = date_format($stop_temp, 'Y-m-d H:i:s');

			/* Convert to php time */
			$stop_ts = strtotime($stop_time);

			/* Validate start and stop time */
			if ($start_ts > $stop_ts)
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Stop time cannot be before start time.');
			}

			/* Calculate difference between stop and start time in minutes */
			$minutes_count = abs($stop_ts - $start_ts) / 60;
			$minutes_count = round($minutes_count, 0);
		}
		else
		{
			$stop_time = NULL;
			$minutes_count = 0;
		}

		$input['start_time'] = $start_time;
		$input['stop_time'] = $stop_time;
		$input['minutes_count'] = $minutes_count;

		$this->timewatchValidator->with($input);

		if ($this->timewatchValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->timewatchValidator->getErrors());
		}
		else
		{
			if ($id)
			{

				$timewatch = Timewatch::curUser()->find($id);
				if (!$timewatch)
				{
					return Redirect::action('TimewatchesController@getStart')
						->with('alert-danger', 'Timewatch not found.');
				}

				$task = Task::find($timewatch->task_id);
				if (!$task)
				{
					return Redirect::action('TimewatchesController@getStart')
						->with('alert-danger', 'Task not found.');
				}

				$goal = Goal::curUser()->find($task->goal_id);
				if (!$goal)
				{
					return Redirect::action('TimewatchesController@getStart')
						->with('alert-danger', 'Goal not found.');
				}

				$timewatch->start_time = $input['start_time'];
				$timewatch->stop_time = $input['stop_time'];
				$timewatch->minutes_count = $minutes_count;
				$timewatch->is_active = $input['is_active'];
				$timewatch->date = date('Y-m-d', $start_ts);

				if (!$timewatch->save())
				{
				        return Redirect::back()->withInput()
	                                        ->with('alert-danger', 'Failed to update timewatch.');
				}

				return Redirect::action('TimewatchesController@getStart')
					->with('alert-success', 'Timewatch udpated.');
			}
			else
			{
				$task = Task::find($input['task_id']);
				if (!$task)
				{
		                        return Redirect::back()->withInput()
						->with('alert-danger', 'Please select a task.');
				}

				$goal = Goal::curUser()->find($task->goal_id);
				if (!$goal)
				{
		                        return Redirect::back()->withInput()
						->with('alert-danger', 'Goal not found.');
				}

				$timewatch = Timewatch::create($input);
				if (!$timewatch)
				{
					return Redirect::back()->withInput()
						->with('alert-danger', 'Failed to create timewatch.');
				}

				/* Update timewatch date */
				$timewatch->date = date('Y-m-d', $start_ts);
				$timewatch->save();

				return Redirect::action('TimewatchesController@getStart')
					->with('alert-success', 'Timewatch created.');
			}
		}
	}

	public function deleteDestroy($id)
	{
                $timewatch = Timewatch::curUser()->find($id);
		if (!$timewatch)
		{
			return Redirect::action('TimewatchesController@getStart')
				->with('alert-danger', 'Timewatch not found.');
		}

                if (!$timewatch->delete())
		{
			return Redirect::action('TimewatchesController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete timewatch.');
		}

		return Redirect::action('TimewatchesController@getStart')
			->with('alert-success', 'Timewatch deleted.');
	}
}
