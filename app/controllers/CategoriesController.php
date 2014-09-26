<?php

use Smartgoalz\Services\Validators\CategoryValidator;

class CategoriesController extends BaseController
{

	protected $categoryValidator;

	public function __construct(CategoryValidator $categoryValidator)
	{
		$this->categoryValidator = $categoryValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Category::curUser()->orderBy('title', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('categories' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Categories not found.'
			));
		}
	}

}
