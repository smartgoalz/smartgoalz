<?php

use Smartgoalz\Services\Validators\ActivityValidator;

class ActivitiesController extends BaseController
{

	protected $activityValidator;

	public function __construct(ActivityValidator $activityValidator)
	{
		$this->activityValidator = $activityValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Activity::curUser()->orderBy('name', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('activities' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activities not found.'
			));
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getTimetable()
	{
		$data = Activity::curUser()->withTimetable()->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('allschedules' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activities not found.'
			));
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getToday($timestamp)
	{
		$weekday = '%' . strtoupper(date("l", $timestamp)) . '%';

		$data = Activity::curUser()->withTimetable()->where('days', 'LIKE', $weekday)->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('activities' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Activities not found.'
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
		$data = Activity::curUser()->find($id);

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('activity' => $data)
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
		$data = Input::get('activity');

		$this->activityValidator->with($data);

		if ($this->activityValidator->passes())
		{
			if (Activity::create($data))
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
				'message' => $this->activityValidator->getErrors()
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
                $activity = Activity::curUser()->find($id);
                if (!$activity)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Activity not found.'
			));
                }

		$data = Input::get('activity');

		$this->activityValidator->with($data);

		if ($this->activityValidator->passes())
		{
			/* Update data */
	                $activity->name = $data['name'];

			if ($activity->save())
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
				'message' => $this->activityValidator->getErrors()
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
                $activity = Activity::curUser()->find($id);
                if (!$activity)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Activity not found.'
			));
                }

                if ($activity->delete())
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
