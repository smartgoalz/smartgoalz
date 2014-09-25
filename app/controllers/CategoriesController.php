<?php

use Smartgoalz\Gateways\CategoryGateway;

class CategoriesController extends BaseController {

	public function __construct(CategoryGateway $categoryGateway)
	{
		$this->categoryGateway = $categoryGateway;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		return Response::json($this->categoryGateway->getAll());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShow($id)
	{
		return Response::json($this->categoryGateway->get($id));
	}

	/**
	 * Create a new resource in storage.
	 *
	 * @return Response
	 */
	public function postCreate()
	{
		return Response::json($this->categoryGateway->create(Input::all()));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		return Response::json($this->categoryGateway->update($id, Input::all()));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteDestroy($id)
	{
		return Response::json($this->categoryGateway->destroy($id));
	}
}
