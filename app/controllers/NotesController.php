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

use Smartgoalz\Services\Validators\NoteValidator;

class NotesController extends BaseController
{

	protected $noteValidator;

	public function __construct(NoteValidator $noteValidator)
	{
		$this->noteValidator = $noteValidator;

                $user = User::find(Auth::id());
                $this->dateformat = $user->dateformat;
	}

	public function getIndex()
	{
		$query = Note::curUser()->orderBy('created_at', 'DESC');

                /* Search */
                $search = Input::get('search');
                if ($search)
                {
			if (strlen($search) >= 1) {
				$query->where('note', 'LIKE', '%'.$search.'%')
					->orWhere('title', 'LIKE', '%'.$search.'%');
			}
                }

		$notes = $query->paginate(10);

		if (!$notes)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'Notes not found.');
		}

		return View::make('notes.index')
			->with('notes', $notes)
			->with('dateformat', $this->dateformat)
			->with('search', $search);
	}

	public function getShow($id)
	{
		$note = Note::curUser()->find($id);

		if (!$note)
		{
			return Redirect::action('NotesController@getIndex')
				->with('alert-danger', 'Note not found.');
		}

		return View::make('notes.show')
			->with('note', $note)
			->with('dateformat', $this->dateformat);
	}

	public function getCreate()
	{
		return View::make('notes.create')
			->with('dateformat', $this->dateformat);
	}

	public function postCreate()
	{
		$input = Input::all();

		if (empty($input['pin_dashboard']))
		{
			$input['pin_dashboard'] = 0;
		}
		else
		{
			$input['pin_dashboard'] = 1;
		}

		if (empty($input['pin_top']))
		{
			$input['pin_top'] = 0;
		}
		else
		{
			$input['pin_top'] = 1;
		}

		$this->noteValidator->with($input);

		if ($this->noteValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->noteValidator->getErrors());
		}
		else
		{
			if (!Note::create($input))
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create note.');
			}

                        return Redirect::action('NotesController@getIndex')
                                ->with('alert-success', 'Note created.');
		}
	}

	public function getEdit($id)
	{
		$note = Note::curUser()->find($id);

		if (!$note)
		{
			return Redirect::action('NotesController@getIndex')
				->with('alert-danger', 'Note not found.');
		}

		return View::make('notes.edit')
			->with('note', $note)
			->with('dateformat', $this->dateformat);
	}

	public function postEdit($id)
	{
		$note = Note::curUser()->find($id);

		if (!$note)
		{
			return Redirect::action('NotesController@getIndex')
				->with('alert-danger', 'Note not found.');
		}

		$input = Input::all();

		if (empty($input['pin_dashboard']))
		{
			$input['pin_dashboard'] = 0;
		}
		else
		{
			$input['pin_dashboard'] = 1;
		}

		if (empty($input['pin_top']))
		{
			$input['pin_top'] = 0;
		}
		else
		{
			$input['pin_top'] = 1;
		}

		$this->noteValidator->with($input);

		if ($this->noteValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->noteValidator->getErrors());
		}
		else
		{
			/* Update data */
	                $note->title = $input['title'];
	                $note->pin_dashboard = $input['pin_dashboard'];
	                $note->pin_top = $input['pin_top'];
	                $note->note = $input['note'];

			if (!$note->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update note.');
			}

                        return Redirect::action('NotesController@getIndex')
                                ->with('alert-success', 'Note updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $note = Note::curUser()->find($id);
                if (!$note)
		{
			return Redirect::action('NotesController@getIndex')
				->with('alert-danger', 'Note not found.');
                }

                if (!$note->delete())
		{
			return Redirect::action('NotesController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete note.');
		}

		return Redirect::action('NotesController@getIndex')
			->with('alert-success', 'Note deleted.');
	}
}
