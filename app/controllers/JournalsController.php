<?php

use Smartgoalz\Services\Validators\JournalValidator;

class JournalsController extends BaseController
{

	protected $journalValidator;

	public function __construct(JournalValidator $journalValidator)
	{
		$this->journalValidator = $journalValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Journal::curUser()->orderBy('created_at', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('journals' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Journal entries not found.'
			));
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		$data = Journal::curUser()->find($id);

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('journal' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Journal entry not found.'
			));
		}
	}

	/**
	 * Create a new resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		$data = Input::get('journal');

		$this->journalValidator->with($data);

		if ($this->journalValidator->passes())
		{
			if (Journal::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Journal entry created.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create journal entry.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->journalValidator->getErrors()
			));
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
                $journal = Journal::curUser()->find($id);
                if (!$journal)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Journal entry not found.'
			));
                }

		$data = Input::get('journal');

		$this->journalValidator->with($data);

		if ($this->journalValidator->passes())
		{
			/* Update data */
	                $journal->title = $data['title'];
	                $journal->date = $data['date'];
	                $journal->entry = $data['entry'];

			if ($journal->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Journal entry updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update journal entry.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->journalValidator->getErrors()
			));
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteDestroy($id)
	{
                $journal = Journal::curUser()->find($id);
                if (!$journal)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Journal entry not found.'
			));
                }

                if ($journal->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Journal entry deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete journal entry.'
			));
		}
	}
}
