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

use Smartgoalz\Services\Validators\JournalValidator;

class JournalsController extends BaseController
{

	protected $journalValidator;

	public function __construct(JournalValidator $journalValidator)
	{
		$this->journalValidator = $journalValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getIndex()
	{
		$query = Journal::curUser()->orderBy('created_at', 'DESC');

                /* Search */
                $search = Input::get('search');
                if ($search)
                {
			if (strlen($search) >= 1) {
				$query->where('entry', 'LIKE', '%'.$search.'%')
					->orWhere('title', 'LIKE', '%'.$search.'%');
			}
                }

		$journals = $query->paginate(10);

		if (!$journals)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'Journal entries not found.');
		}

		return View::make('journals.index')
			->with('journals', $journals)
			->with('dateformat', $this->dateformat)
			->with('search', $search);
	}

	public function getShow($id)
	{
		$journal = Journal::curUser()->find($id);

		if (!$journal)
		{
			return Redirect::action('JournalsController@getIndex')
				->with('alert-danger', 'Journal entry not found.');
		}

		return View::make('journals.show')
			->with('journal', $journal)
			->with('dateformat', $this->dateformat);
	}

	public function getCreate()
	{
		return View::make('journals.create')
			->with('dateformat', $this->dateformat);
	}

	public function postCreate()
	{
		$input = Input::all();

		/* Format date */
                $date_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' h:i A',
                        $input['date']
                );
                if (!$date_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid date.');
                }
                $date = date_format($date_temp, 'Y-m-d H:i:s');

		$input['date'] = $date;

		$this->journalValidator->with($input);

		if ($this->journalValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->journalValidator->getErrors());
		}
		else
		{
			if (!Journal::create($input))
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create journal entry.');
			}

                        return Redirect::action('JournalsController@getIndex')
                                ->with('alert-success', 'Journal entry created.');
		}
	}

	public function getEdit($id)
	{
		$journal = Journal::curUser()->find($id);

		if (!$journal)
		{
			return Redirect::action('JournalsController@getIndex')
				->with('alert-danger', 'Journal entry not found.');
		}

		/* Format date */
                $date_temp = date_create_from_format('Y-m-d H:i:s', $journal->date);
                if (!$date_temp)
                {
                        $date = '';
                }
		else
		{
			$date = date_format($date_temp, explode('|', $this->dateformat)[0]);
		}

		return View::make('journals.edit')
			->with('journal', $journal)
			->with('date', $date)
			->with('dateformat', $this->dateformat);
	}

	public function postEdit($id)
	{
		$journal = Journal::curUser()->find($id);

		if (!$journal)
		{
			return Redirect::action('JournalsController@getIndex')
				->with('alert-danger', 'Journal entry not found.');
		}

		$input = Input::all();

		/* Format date */
                $date_temp = date_create_from_format(
                        explode('|', $this->dateformat)[0] . ' h:i A',
                        $input['date']
                );
                if (!$date_temp)
                {
                        return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid date.');
                }
                $date = date_format($date_temp, 'Y-m-d H:i:s');

		$input['date'] = $date;

		$this->journalValidator->with($input);

		if ($this->journalValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->journalValidator->getErrors());
		}
		else
		{
			/* Update data */
	                $journal->title = $input['title'];
	                $journal->date = $input['date'];
	                $journal->entry = $input['entry'];

			if (!$journal->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update journal entry.');
			}

                        return Redirect::action('JournalsController@getIndex')
                                ->with('alert-success', 'Journal entry updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $journal = Journal::curUser()->find($id);
                if (!$journal)
		{
			return Redirect::action('JournalsController@getIndex')
				->with('alert-danger', 'Journal entry not found.');
                }

                if (!$journal->delete())
		{
			return Redirect::action('JournalsController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete journal entry.');
		}

		return Redirect::action('JournalsController@getIndex')
			->with('alert-success', 'Journal entry deleted.');
	}
}
