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

use Smartgoalz\Services\Validators\TimetableValidator;

class TimetablesController extends BaseController
{

	protected $timetableValidator;

	public function __construct(TimetableValidator $timetableValidator)
	{
		$this->timetableValidator = $timetableValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getIndex($timestamp = null)
	{
		if (!$timestamp)
		{
			$timestamp = time();
		}
		$weekday = strtoupper(date("l", $timestamp));

		$schedules = Activity::curUser()->withTimetable()
			->where('days', 'LIKE', '%' . $weekday . '%')
			->orderBy('from_time')->get();

		if (!$schedules)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'No schedule for today.');
		}

		return View::make('timetables.index')
			->with('dateformat', $this->dateformat)
			->with('schedules', $schedules);
	}

	public function getShow($timestamp)
	{
		if (!$timestamp)
		{
			return Redirect::action('TimetablesController@getIndex');
		}

		$weekday = strtoupper(date("l", $timestamp));

		$schedules = Activity::curUser()->withTimetable()
			->where('days', 'LIKE', '%' . $weekday . '%')
			->orderBy('from_time')->get();

		if (!$schedules)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'No schedule for today.');
		}

		return View::make('timetables.show')
			->with('timestamp', $timestamp)
			->with('dateformat', $this->dateformat)
			->with('schedules', $schedules);
	}

	public function getManage()
	{
		$activities = Activity::curUser()->orderBy('title', 'DESC')->get();

		if (!$activities)
		{
			return Redirect::action('TimetablesController@getIndex')
				->with('alert-danger', 'Activities not found.');
		}

		$full_activities = Activity::curUser()->withTimetable()
			->orderBy('from_time')->get();

		$timetable = array();
		foreach ($full_activities as $item)
		{
			if (strpos($item->days, 'SUNDAY') !== false)
			{
				$timetable['SUNDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
			if (strpos($item->days, 'MONDAY') !== false)
			{
				$timetable['MONDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
			if (strpos($item->days, 'TUESDAY') !== false)
			{
				$timetable['TUESDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
			if (strpos($item->days, 'WEDNESDAY') !== false)
			{
				$timetable['WEDNESDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
			if (strpos($item->days, 'THURSDAY') !== false)
			{
				$timetable['THURSDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
			if (strpos($item->days, 'FRIDAY') !== false)
			{
				$timetable['FRIDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
			if (strpos($item->days, 'SATURDAY') !== false)
			{
				$timetable['SATURDAY'][] = array(
					'id' => $item->id,
					'activity_id' => $item->activities_id,
					'title' => $item->activities_title,
					'from_time' => $item->from_time,
					'to_time' => $item->to_time,
				);
			}
		}

		return View::make('timetables.manage')
			->with('activities', $activities)
			->with('timetable', $timetable);
	}

	public function getSchedule($id)
	{
		$activity = Activity::curUser()->find($id);
		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		$schedules = Timetable::where('activity_id', $id)->orderBy('from_time', 'ASC')->get();

		if (!$schedules)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Schedule not found.');
		}

		return View::make('timetables.schedule')
			->with('schedules', $schedules)
			->with('activity', $activity)
			->with('dateformat', $this->dateformat);
	}

	public function getCreate($activity_id)
	{
		$activity = Activity::curUser()->find($activity_id);
		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		$days = array(
			'SUNDAY' => 'Sunday',
			'MONDAY' => 'Monday',
			'TUESDAY' => 'Tuesday',
			'WEDNESDAY' => 'Wednesday',
			'THURSDAY' => 'Thursday',
			'FRIDAY' => 'Friday',
			'SATURDAY' => 'Saturday',
		);

		return View::make('timetables.create')
			->with('activity', $activity)
			->with('days', $days)
			->with('dateformat', $this->dateformat);
	}

	public function postCreate($activity_id)
	{
		$activity = Activity::curUser()->find($activity_id);
		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		$input = Input::all();

		/* Format from time */
                $from_temp = date_create_from_format('Y-m-d H:i A',
                        '2000-01-01 ' . $input['from_time']
                );
                if (!$from_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid From time.');
                }
                $from_time = date_format($from_temp, 'H:i:s');

		/* Format to date */
                $to_temp = date_create_from_format('Y-m-d H:i A',
                        '2000-01-01 ' . $input['to_time']
                );
                if (!$to_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid To time.');
                }
                $to_time = date_format($to_temp, 'H:i:s');

		/* Check if rfom time if before to time */
		if ($from_temp > $to_temp)
		{
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'From time cannot be after To time.');
		}

		if (empty($input['days']))
		{
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Please select atleast one day.');
		}

		$input['from_time'] = $from_time;
		$input['to_time'] = $to_time;
		$input['days'] = join(',', $input['days']);

		$this->timetableValidator->with($input);

		if ($this->timetableValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->timetableValidator->getErrors());
		}
		else
		{
			$timetable = new Timetable($input);
			$timetable->activity()->associate($activity);

			if (!$timetable->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create schedule.');
			}

			return Redirect::action('TimetablesController@getSchedule', array($activity->id))
				->with('alert-success', 'Schedule added to activity.');
		}
	}

	public function getEdit($activity_id, $id)
	{
		$activity = Activity::curUser()->find($activity_id);
		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		$timetable = Timetable::find($id);
		if (!$timetable)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Schedule not found.');
		}

		if ($timetable->activity_id != $activity->id)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Schedule does not belong to activity.');
		}

		/* Format from time */
                $from_temp = date_create_from_format('Y-m-d H:i:s', '2000-01-01 ' . $timetable->from_time);
                if (!$from_temp)
                {
                        $from_time = '';
                }
		else
		{
			$from_time = date_format($from_temp, 'H:i A');
		}

		/* Format to time */
                $to_temp = date_create_from_format('Y-m-d H:i:s', '2000-01-01 ' . $timetable->to_time);
                if (!$to_temp)
                {
			$to_time = '';
                }
		else
		{
			$to_time = date_format($to_temp, 'H:i A');
		}

		$selected_days = explode(',', $timetable->days);

		$days = array(
			'SUNDAY' => 'Sunday',
			'MONDAY' => 'Monday',
			'TUESDAY' => 'Tuesday',
			'WEDNESDAY' => 'Wednesday',
			'THURSDAY' => 'Thursday',
			'FRIDAY' => 'Friday',
			'SATURDAY' => 'Saturday',
		);

		return View::make('timetables.edit')
			->with('activity', $activity)
			->with('timetable', $timetable)
			->with('days', $days)
			->with('from_time', $from_time)
			->with('to_time', $to_time)
			->with('selected_days', $selected_days)
			->with('dateformat', $this->dateformat);
	}

	public function postEdit($activity_id, $id)
	{
		$activity = Activity::curUser()->find($activity_id);
		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		$timetable = Timetable::find($id);
		if (!$timetable)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Schedule not found.');
		}

		if ($timetable->activity_id != $activity->id)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Schedule does not belong to activity.');
		}

		$input = Input::all();

		/* Format from time */
                $from_temp = date_create_from_format('Y-m-d H:i A',
                        '2000-01-01 ' . $input['from_time']
                );
                if (!$from_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid From time.');
                }
                $from_time = date_format($from_temp, 'H:i:s');

		/* Format to date */
                $to_temp = date_create_from_format('Y-m-d H:i A',
                        '2000-01-01 ' . $input['to_time']
                );
                if (!$to_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid To time.');
                }
                $to_time = date_format($to_temp, 'H:i:s');

		/* Check if rfom time if before to time */
		if ($from_temp > $to_temp)
		{
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'From time cannot be after To time.');
		}

		if (empty($input['days']))
		{
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Please select atleast one day.');
		}

		$input['from_time'] = $from_time;
		$input['to_time'] = $to_time;
		$input['days'] = join(',', $input['days']);

		$this->timetableValidator->with($input);

		if ($this->timetableValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->timetableValidator->getErrors());
		}
		else
		{
			$timetable->from_time = $input['from_time'];
			$timetable->to_time = $input['to_time'];
			$timetable->days = $input['days'];

			if (!$timetable->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update schedule.');
			}

			return Redirect::action('TimetablesController@getSchedule', array($activity->id))
				->with('alert-success', 'Schedule updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $schedule = Timetable::find($id);
                if (!$schedule)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Schedule not found.');
                }

		$activity = Activity::curUser()->find($schedule->activity_id);
		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

                if (!$schedule->delete())
		{
			return Redirect::action('TimetablesController@getSchedule', array($activity->id))
				->with('alert-danger', 'Oops ! Failed to delete schedule.');
		}

		return Redirect::action('TimetablesController@getSchedule', array($activity->id))
			->with('alert-success', 'Schedule deleted.');
	}
}
