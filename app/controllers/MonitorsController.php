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

use Smartgoalz\Services\Validators\MonitorValidator;

class MonitorsController extends BaseController
{

	protected $monitorValidator;

	public function __construct(MonitorValidator $monitorValidator)
	{
		$this->monitorValidator = $monitorValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getIndex()
	{
		$monitors = Monitor::curUser()->orderBy('title', 'DESC')->get();

		if (!$monitors)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'Monitors not found.');
		}

		return View::make('monitors.index')
			->with('monitors', $monitors)
			->with('dateformat', $this->dateformat);
	}

	public function getShow($id)
	{
		$monitor = Monitor::curUser()->find($id);

		if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
		}

		$monitorvalues = Monitorvalue::where('monitor_id', '=', $monitor->id)
			->orderBy('date')->paginate(20);

		return View::make('monitors.show')
			->with('monitor', $monitor)
			->with('monitorvalues', $monitorvalues)
			->with('dateformat', $this->dateformat);
	}

	public function getCreate()
	{
		return View::make('monitors.create')
			->with('dateformat', $this->dateformat);
	}

	public function postCreate()
	{
		$input = Input::all();

		/* Check if monitor title is unique for a user */
		$monitors = Monitor::curUser()->like('title', $input['title']);

		if ($monitors->count() >= 1)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Monitor with same name already exists.');
		}

		if ($input['is_lower_better'] == 'LOWER')
		{
			$input['is_lower_better'] = 1;
		}
		else
		{
			$input['is_lower_better'] = 0;
		}

		$this->monitorValidator->with($input);

		if ($this->monitorValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->monitorValidator->getErrors());
		}
		else
		{
			if (!Monitor::create($input))
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create monitor.');
			}

                        return Redirect::action('MonitorsController@getIndex')
                                ->with('alert-success', 'Monitor created.');
		}
	}

	public function getEdit($id)
	{
                $monitor = Monitor::curUser()->find($id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

		if ($monitor->is_lower_better == 1)
		{
			$is_lower_better = 'LOWER';
		}
		else
		{
			$is_lower_better = 'HIGHER';
		}

		return View::make('monitors.edit')
			->with('monitor', $monitor)
			->with('is_lower_better', $is_lower_better)
			->with('dateformat', $this->dateformat);
	}

	public function postEdit($id)
	{
                $monitor = Monitor::curUser()->find($id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

		$input = Input::all();

		/* Check if monitor title is unique for a user */
		$monitors = Monitor::curUser()->like('title', $input['title'])
			->where('id', '!=', $id);

		if ($monitors->count() >= 1)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Monitor with same name already exists.');
		}

		if ($input['is_lower_better'] == 'LOWER')
		{
			$input['is_lower_better'] = 1;
		}
		else
		{
			$input['is_lower_better'] = 0;
		}

		$this->monitorValidator->with($input);

		if ($this->monitorValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->monitorValidator->getErrors());
		}
		else
		{
			/* Update data */
	                $monitor->title = $input['title'];
	                $monitor->type = $input['type'];
	                $monitor->minimum = $input['minimum'];
	                $monitor->maximum = $input['maximum'];
	                $monitor->minimum_threshold = $input['minimum_threshold'];
	                $monitor->maximum_threshold = $input['maximum_threshold'];
	                $monitor->is_lower_better = $input['is_lower_better'];
	                $monitor->units = $input['units'];
	                $monitor->frequency = $input['frequency'];
	                $monitor->description = $input['description'];

			if (!$monitor->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update monitor.');
			}

                        return Redirect::action('MonitorsController@getIndex')
                                ->with('alert-success', 'Monitor updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $monitor = Monitor::curUser()->find($id);

		if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
		}

                $monitor->monitorvalues()->delete();

                if (!$monitor->delete())
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete monitor.');
		}

		return Redirect::action('MonitorsController@getIndex')
			->with('alert-success', 'Monitor deleted.');
	}
}
