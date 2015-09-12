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

use Smartgoalz\Services\Validators\ActivityValidator;

class ActivitiesController extends BaseController
{

	protected $activityValidator;

	public function __construct(ActivityValidator $activityValidator)
	{
		$this->activityValidator = $activityValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getCreate()
	{
		return View::make('activities.create');
	}

	public function postCreate()
	{
		$input = Input::all();

		/* Check if activity title is unique for a user */
		$activities = Activity::curUser()->like('title', $input['title']);

		if ($activities->count() >= 1)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Activity with same title already exists.');
		}

		$this->activityValidator->with($input);

		if ($this->activityValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->activityValidator->getErrors());
		}
		else
		{
			if (!Activity::create($input))
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create activity.');
			}

                        return Redirect::action('TimetablesController@getManage')
                                ->with('alert-success', 'Activity created.');
		}
	}

	public function getEdit($id)
	{
                $activity = Activity::curUser()->find($id);

		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		return View::make('activities.edit')->with('activity', $activity);
	}

	public function postEdit($id)
	{
                $activity = Activity::curUser()->find($id);

		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

		$input = Input::all();

		/* Check if activity title is unique for a user */
		$activities = Activity::curUser()->like('title', $input['title'])
			->where('id', '!=', $id);

		if ($activities->count() >= 1)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Activity with same title already exists.');
		}

		$this->activityValidator->with($input);

		if ($this->activityValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->activityValidator->getErrors());
		}
		else
		{
			/* Update data */
	                $activity->title = $input['title'];

			if (!$activity->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update activity.');
			}

                        return Redirect::action('TimetablesController@getManage')
                                ->with('alert-success', 'Activity updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $activity = Activity::curUser()->find($id);

		if (!$activity)
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Activity not found.');
		}

                $activity->timetables()->delete();

                if (!$activity->delete())
		{
			return Redirect::action('TimetablesController@getManage')
				->with('alert-danger', 'Oops ! Failed to delete activity.');
		}

		return Redirect::action('TimetablesController@getManage')
			->with('alert-success', 'Activity deleted.');
	}
}
