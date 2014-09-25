<?php

use Smartgoalz\Gateways\NoteGateway;

class NotesController extends BaseController {

	public function __construct(NoteGateway $noteGateway)
	{
		$this->noteGateway = $noteGateway;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		return Response::json($this->noteGateway->getAll());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		return Response::json($this->noteGateway->get($id));
	}

	/**
	 * Create a new resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		return Response::json($this->noteGateway->create(Input::all()));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		return Response::json($this->noteGateway->update($id, Input::all()));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteDestroy($id)
	{
		return Response::json($this->noteGateway->destroy($id));
	}
}
