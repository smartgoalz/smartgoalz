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
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getSchedule($id)
	{
		$activity = Activity::curUser()->find($id);
		if (!$activity)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activity not found.'
			));
		}

		$data = Timetable::where('activity_id', $id)->orderBy('from_time', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array(
					'activity' => $activity,
					'timetables' => $data
				)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Schedule not found.'
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

		$activity = Activity::curUser()->find($data['activity_id']);
		if (!$activity)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activity not found.'
			));
		}

		$this->timetableValidator->with($data);

		if ($this->timetableValidator->passes())
		{
			if (Timetable::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Schedule created.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create schedule.'
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
                $timetable = Timetable::find($id);
                if (!$timetable)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Schedule not found.'
			));
                }

		$data = Input::get('timetable');

		$activity = Activity::curUser()->find($timetable->activity_id);
		if (!$activity) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activity not found.'
			));
		}

		$this->timetableValidator->with($data);

		if ($this->timetableValidator->passes())
		{
			/* Update data */
	                $timetable->from_time = $data['from_time'];
	                $timetable->to_time = $data['to_time'];
	                $timetable->days = $data['days'];

			if ($timetable->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Schedule updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update schedule.'
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
                $timetable = Timetable::find($id);
                if (!$timetable)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Schedule not found.'
			));
                }

		$activity = Activity::curUser()->find($timetable->activity_id);
		if (!$activity)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activity not found.'
			));
		}

                if ($timetable->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Schedule deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete schedule.'
			));
		}
	}
}
