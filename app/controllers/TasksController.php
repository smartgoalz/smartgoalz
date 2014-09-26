<?php

use Smartgoalz\Services\Validators\TaskValidator;

class TasksController extends BaseController
{

	public function __construct(TaskValidator $taskValidator)
	{
		$this->taskValidator = $taskValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex($goal_id)
	{
		$goal = Goal::curUser()->find($goal_id);
		if (!$goal) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
			));
		}

		$data = Task::where('goal_id', $goal_id)->orderBy('weight', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('tasks' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Tasks not found.'
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
		return Response::json($this->taskGateway->create(Input::all()));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putUpdate($id)
	{
		return Response::json($this->taskGateway->update($id, Input::all()));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteDestroy($id)
	{
                $task = Task::find($id);
                if (!$task)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Task not found.'
			));
                }

                $goal = Goal::find($task->goal_id);
                if (!$goal)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Goal not found.'
			));
                }

                if ($task->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Task deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete task.'
			));
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putDone($id)
	{
                $task = Task::find($id);
                if (!$task)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Task not found.'
			));
                }

                $goal = Goal::find($task->goal_id);
                if (!$goal)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Goal not found.'
			));
                }

		$task->is_completed = 1;

                if ($task->save())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Congratulations ! You have completed a task.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to mark task as completed.'
			));
		}
	}
}
