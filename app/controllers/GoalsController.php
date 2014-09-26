<?php

use Smartgoalz\Services\Validators\GoalValidator;

class GoalsController extends BaseController
{

	protected $goalValidator;

	public function __construct(GoalValidator $goalValidator)
	{
		$this->goalValidator = $goalValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Goal::curUser()->orderBy('title', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('goals' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goals not found.'
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
		$data = Goal::curUser()->find($id);

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('goal' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
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
		$data = Input::get('goal');

		$this->goalValidator->with($data);

		if ($this->goalValidator->passes())
		{
			/* Set default values for a new goal */
			$data['is_completed'] = 0;
			$data['completion_date'] = NULL;
			$data['task_total'] = 0;
			$data['task_completed'] = 0;
			$data['status'] = 'ACTIVE';

			if (Goal::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Congratulations ! Goal created.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create goal.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->goalValidator->getErrors()
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
                $goal = Goal::curUser()->find($id);
                if (!$goal)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Goal not found.'
			));
                }

		$data = Input::get('goal');

		unset($data['is_completed']);
		unset($data['completion_date']);
		unset($data['task_total']);
		unset($data['task_completed']);

		$this->goalValidator->with($data);

		if ($this->goalValidator->passes())
		{
			/* Update data */
	                $goal->title = $data['title'];
			$goal->category_id = $data['category_id'];
	                $goal->start_date = $data['start_date'];
	                $goal->due_date = $data['due_date'];
	                $goal->difficulty = $data['difficulty'];
	                $goal->priority = $data['priority'];
	                $goal->reason = $data['reason'];

			if ($goal->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Goal updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update goal.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->goalValidator->getErrors()
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
                $goal = Goal::curUser()->find($id);
                if (!$goal)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Goal not found.'
			));
                }

                if ($goal->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Goal deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete goal.'
			));
		}
	}
}
