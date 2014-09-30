<?php

use Smartgoalz\Services\Validators\TimetableValidator;

class TimetablesController extends BaseController
{

	protected $timetableValidator;

	public function __construct(TimetableValidator $timetableValidator)
	{
		$this->timetableValidator = $timetableValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Timetable::curUser()->orderBy('from_time', 'ASC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('timetables' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Timetable not found.'
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
		$data = Timetable::curUser()->find($id);

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('timetable' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activity not found.'
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
		$data = Input::get('timetable');

		$this->timetableValidator->with($data);

		if ($this->timetableValidator->passes())
		{
			if (Timetable::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Activity created.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create activity.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->timetableValidator->getErrors()
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
                $timetable = Timetable::curUser()->find($id);
                if (!$timetable)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Activity not found.'
			));
                }

		$data = Input::get('timetable');

		$this->timetableValidator->with($data);

		if ($this->timetableValidator->passes())
		{
			/* Update data */
	                $timetable->activity = $data['activity'];
	                $timetable->from_time = $data['from_time'];
	                $timetable->to_time = $data['to_time'];
	                $timetable->days = $data['days'];
	                $timetable->track = $data['track'];

			if ($timetable->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Activity updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update activity.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->timetableValidator->getErrors()
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
                $timetable = Timetable::curUser()->find($id);
                if (!$timetable)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Activity not found.'
			));
                }

                if ($timetable->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Activity deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete activity.'
			));
		}
	}
}
