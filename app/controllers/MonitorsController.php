<?php

use Smartgoalz\Services\Validators\MonitorValidator;

class MonitorsController extends BaseController
{

	protected $monitorValidator;

	public function __construct(MonitorValidator $monitorValidator)
	{
		$this->monitorValidator = $monitorValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Monitor::curUser()->orderBy('title', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('monitors' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Monitors not found.'
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
		$data = Monitor::curUser()->find($id);

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('monitor' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Monitor not found.'
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
		$data = Input::get('monitor');

		$this->monitorValidator->with($data);

		if ($this->monitorValidator->passes())
		{
			if (Monitor::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Monitor created.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create monitor.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->monitorValidator->getErrors()
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
                $monitor = Monitor::curUser()->find($id);
                if (!$monitor)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Monitor not found.'
			));
                }

		$data = Input::get('monitor');

		$this->monitorValidator->with($data);

		if ($this->monitorValidator->passes())
		{
			/* Update data */
	                $monitor->title = $data['title'];
	                $monitor->type = $data['type'];
	                $monitor->minimum = $data['minimum'];
	                $monitor->maximum = $data['maximum'];
	                $monitor->minimum_threshold = $data['minimum_threshold'];
	                $monitor->maximum_threshold = $data['maximum_threshold'];
	                $monitor->is_lower_better = $data['is_lower_better'];
	                $monitor->units = $data['units'];
	                $monitor->frequency = $data['frequency'];
	                $monitor->description = $data['description'];

			if ($monitor->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Monitor updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update monitor.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->monitorValidator->getErrors()
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
                $monitor = Monitor::curUser()->find($id);
                if (!$monitor)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Monitor not found.'
			));
                }

                if ($monitor->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Monitor deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete monitor.'
			));
		}
	}
}
