<?php

use Smartgoalz\Services\Validators\MonitorvalueValidator;

class MonitorvaluesController extends BaseController
{

	protected $monitorvalueValidator;

	public function __construct(MonitorvalueValidator $monitorvalueValidator)
	{
		$this->monitorvalueValidator = $monitorvalueValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex($monitor_id)
	{
		$monitor = Monitor::curUser()->find($monitor_id);
		if (!$monitor) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Monitor not found.'
			));
		}

		$data = Monitorvalue::where('monitor_id', $monitor_id)->orderBy('date', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('monitorvalues' => $data)
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
	 * @return Response
	 */
	public function getShow($id)
	{
                $monitorvalue = Monitorvalue::find($id);
                if (!$monitorvalue)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Value not found.'
			));
                }

		$monitor = Monitor::curUser()->find($monitorvalue->monitor_id);
		if (!$monitor) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Monitor not found.'
			));
		}

		if ($monitorvalue)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('monitorvalue' => $monitorvalue)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Value not found.'
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
		$data = Input::get('monitorvalue');

                $monitor = Monitor::curUser()->find($data['monitor_id']);
                if (!$monitor)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Monitor not found.'
			));
                }

		$this->monitorvalueValidator->with($data);

		if ($this->monitorvalueValidator->passes())
		{
			if (Monitorvalue::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Value added.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to add value.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->monitorvalueValidator->getErrors()
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
                $monitorvalue = Monitorvalue::find($id);
                if (!$monitorvalue)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Value not found.'
			));
                }

        	$monitor = Monitor::find($monitorvalue->monitor_id);
                if (!$monitor)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Monitor not found.'
			));
                }

		$data = Input::get('monitorvalue');

		$this->monitorvalueValidator->with($data);

		if ($this->monitorvalueValidator->passes())
		{
			/* Update data */
	                $monitorvalue->value = $data['value'];
	                $monitorvalue->date = $data['date'];

			if ($monitorvalue->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Value updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update value.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->monitorvalueValidator->getErrors()
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
                $monitorvalue = Monitorvalue::find($id);
                if (!$monitorvalue)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Value not found.'
			));
                }

        	$monitor = Monitor::find($monitorvalue->monitor_id);
                if (!$monitor)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Monitor not found.'
			));
                }

                if ($monitorvalue->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Value deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete value.'
			));
		}
	}
}
