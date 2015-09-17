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

use Smartgoalz\Services\Validators\MonitorvalueValidator;

class MonitorvaluesController extends BaseController
{

	protected $monitorvalueValidator;

	public function __construct(MonitorvalueValidator $monitorvalueValidator)
	{
		$this->monitorvalueValidator = $monitorvalueValidator;

		$user = User::find(Auth::id());
		$this->dateformat_php = $user->dateformat_php;
		$this->dateformat_cal = $user->dateformat_cal;
		$this->dateformat_js = $user->dateformat_js;
	}

	public function getCreate($monitor_id)
	{
                $monitor = Monitor::curUser()->find($monitor_id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

		return View::make('monitorvalues.create')
			->with('monitor', $monitor)
			->with('dateformat_cal', $this->dateformat_cal);
	}

	public function postCreate($monitor_id)
	{
                $monitor = Monitor::curUser()->find($monitor_id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

		$input = Input::all();

		/* Format date */
                $date_temp = date_create_from_format(
                        $this->dateformat_php . ' h:i A', $input['date']
                );
                if (!$date_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid date.');
                }
                $date = date_format($date_temp, 'Y-m-d H:i:s');

		$input['date'] = $date;

		$this->monitorvalueValidator->with($input);

		if ($this->monitorvalueValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->monitorvalueValidator->getErrors());
		}
		else
		{
			$monitorvalue = new Monitorvalue($input);
			$monitorvalue->monitor()->associate($monitor);

			if (!$monitorvalue->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to add value to monitor.');
			}

                        return Redirect::action('MonitorsController@getShow', array($monitor->id))
                                ->with('alert-success', 'Value added to monitor.');
		}
	}

	public function getEdit($monitor_id, $id)
	{
                $monitor = Monitor::curUser()->find($monitor_id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

		$monitorvalue = Monitorvalue::find($id);
                if (!$monitorvalue)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor value not found.');
                }

		if ($monitorvalue->monitor_id != $monitor->id)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor value does not belong to monitor.');
		}

		/* Format date */
                $date_temp = date_create_from_format('Y-m-d H:i:s', $monitorvalue->date);
                if (!$date_temp)
                {
                        $date = '';
                }
		else
		{
			$date = date_format($date_temp, $this->dateformat_php .  ' h:i A');
		}

		return View::make('monitorvalues.edit')
			->with('monitor', $monitor)
			->with('monitorvalue', $monitorvalue)
			->with('date', $date)
			->with('dateformat_cal', $this->dateformat_cal);
	}

	public function postEdit($monitor_id, $id)
	{
                $monitor = Monitor::curUser()->find($monitor_id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

		$monitorvalue = Monitorvalue::find($id);
                if (!$monitorvalue)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor value not found.');
                }

		if ($monitorvalue->monitor_id != $monitor->id)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor value does not belong to monitor.');
		}

		$input = Input::all();

		/* Format date */
                $date_temp = date_create_from_format(
			$this->dateformat_php . ' h:i A', $input['date']
                );
                if (!$date_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid date.');
                }
                $date = date_format($date_temp, 'Y-m-d H:i:s');

		$input['date'] = $date;

		$this->monitorvalueValidator->with($input);

		if ($this->monitorvalueValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->monitorvalueValidator->getErrors());
		}
		else
		{
			/* Update data */
	                $monitorvalue->value = $input['value'];
	                $monitorvalue->date = $input['date'];

			if (!$monitorvalue->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update value.');
			}

                        return Redirect::action('MonitorsController@getShow', array($monitor->id))
                                ->with('alert-success', 'Monitor value updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $monitorvalue = Monitorvalue::find($id);
                if (!$monitorvalue)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor value not found.');
                }

		$monitor = Monitor::curUser()->find($monitorvalue->monitor_id);
                if (!$monitor)
		{
			return Redirect::action('MonitorsController@getIndex')
				->with('alert-danger', 'Monitor not found.');
                }

                if (!$monitorvalue->delete())
		{
			return Redirect::action('MonitorsController@getShow', array($monitor->id))
				->with('alert-danger', 'Oops ! Failed to delete monitor value.');
		}

		return Redirect::action('MonitorsController@getShow', array($monitor->id))
			->with('alert-success', 'Monitor value deleted.');
	}
}
