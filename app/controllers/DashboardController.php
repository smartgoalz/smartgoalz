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

class DashboardController extends BaseController
{
	public function __construct()
	{
		/* If database name is set in database config, need to install application */
		if (Config::get('database.connections.mysql.database') == '')
		{
			return Redirect::action('SetupController@getInstall');
		}
	}

	public function getIndex()
	{
		/* Active Goals */
		$dashboard['active_goals'] =
			Goal::curUser()->where('is_completed', '=', 0)->count();

		$dashboard['pending_tasks'] =
			Task::withGoals()->where('tasks.is_completed', '=', 0)->count();

		$dashboard['active_timewatches'] =
			Timewatch::curUser()->where('is_active', '=', 1)->count();

		$dashboard['notes_dashboard'] =
			Note::curUser()->where('pin_dashboard', '=', 1)->get();

		return View::make('dashboard.index')
			->with('dashboard', $dashboard);
	}
}
