<?php

use Smartgoalz\Gateways\JournalGateway;

class JournalsController extends BaseController {

	public function __construct(JournalGateway $journalGateway)
	{
		$this->journalGateway = $journalGateway;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		return Response::json($this->journalGateway->getAll());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		return Response::json($this->journalGateway->get($id));
	}

	/**
	 * Create a new resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		return Response::json($this->journalGateway->create(Input::all()));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		return Response::json($this->journalGateway->update($id, Input::all()));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteDestroy($id)
	{
		return Response::json($this->journalGateway->destroy($id));
	}
}
