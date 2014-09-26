<?php

use Smartgoalz\Services\Validators\NoteValidator;

class NotesController extends BaseController
{

	protected $noteValidator;

	public function __construct(NoteValidator $noteValidator)
	{
		$this->noteValidator = $noteValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Note::curUser()->orderBy('created_at', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('notes' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Notes not found.'
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
		$data = Note::curUser()->find($id);

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('note' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Note not found.'
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
		$data = Input::get('note');

		$this->noteValidator->with($data);

		if ($this->noteValidator->passes())
		{
			if (Note::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Note created.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create note.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->noteValidator->getErrors()
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
                $note = Note::curUser()->find($id);
                if (!$note)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Note not found.'
			));
                }

		$data = Input::get('note');

		$this->noteValidator->with($data);

		if ($this->noteValidator->passes())
		{
			/* Update data */
	                $note->title = $data['title'];
	                $note->pin_dashboard = $data['pin_dashboard'];
	                $note->pin_top = $data['pin_top'];
	                $note->note = $data['note'];

			if ($note->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Note updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update note.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->noteValidator->getErrors()
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
                $note = Note::curUser()->find($id);
                if (!$note)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Note not found.'
			));
                }

                if ($note->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Note deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete note.'
			));
		}
	}
}
