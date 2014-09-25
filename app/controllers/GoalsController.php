<?php

use Smartgoalz\Gateways\GoalGateway;

class GoalsController extends BaseController {

	public function __construct(GoalGateway $goalGateway)
	{
		$this->goalGateway = $goalGateway;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		return Response::json($this->goalGateway->getAll());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		return Response::json($this->goalGateway->get($id));
	}

	/**
	 * Create a new resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		return Response::json($this->goalGateway->create(Input::all()));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		return Response::json($this->goalGateway->update($id, Input::all()));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteDestroy($id)
	{
		return Response::json($this->goalGateway->destroy($id));
	}
}
