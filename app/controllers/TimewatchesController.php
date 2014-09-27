<?php

use Smartgoalz\Services\Validators\TimewatchValidator;
use Smartgoalz\Services\Validators\TimewatchStopValidator;

class TimewatchesController extends BaseController
{

	protected $timewatchValidator;

	protected $timewatchStopValidator;

	public function __construct(TimewatchValidator $timewatchValidator,
		TimewatchStopValidator $timewatchStopValidator)
	{
		$this->timewatchValidator = $timewatchValidator;
		$this->timewatchStopValidator = $timewatchStopValidator;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$data = Timewatch::curUser()->withTasks()->where('is_active', 0)
			->orderBy('date', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('timewatches' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Timewatches not found.'
			));
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getActive()
	{
		$data = Timewatch::curUser()->withTasks()->where('is_active', 1)
			->orderBy('date', 'DESC')->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('active_timewatches' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Active timewatches not found.'
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
		$timewatch = Timewatch::curUser()->find($id);
		if (!$timewatch) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Timewatch not found.'
			));
		}

		$task = Task::find($timewatch->task_id);
		if (!$task) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Task not found.'
			));
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
			));
		}

		return Response::json(array(
			'status' => 'success',
			'data' => array(
				'goal' => $goal,
				'task' => $task,
				'timewatch' => $timewatch,
			)
		));
	}

	/**
	 * Create a new resource in storage.
	 *
	 * @return Response
	 */
	public function postStart()
	{
		$data = Input::get('timewatch');

		if (empty($data['goal_id'])) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Please select a goal.'
			));
		}
		if (empty($data['task_id'])) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Please select a task.'
			));
		}

		$task = Task::find($data['task_id']);
		if (!$task) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Task not found.'
			));
		}

		if ($data['goal_id'] != $task->goal_id) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Task does not belong to the goal.'
			));
		}

		$goal = Goal::curUser()->find($data['goal_id']);
		if (!$goal) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
			));
		}
		unset($data['goal_id']);

		$data['stop_time'] = NULL;
		$data['is_active'] = 1;

		$this->timewatchValidator->with($data);

		if ($this->timewatchValidator->passes())
		{
			$timewatch = Timewatch::create($data);
			if ($timewatch)
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Timewatch added.',
					'data' => array('id' => $timewatch->id)
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to add timewatch.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->timewatchValidator->getErrors()
			));
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function putStop($id)
	{
		$data = Input::get('timewatch');

                $timewatch = Timewatch::curUser()->find($id);
                if (!$timewatch)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Timewatch not found.'
			));
                }

		$task = Task::find($timewatch->task_id);
		if (!$task) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Task not found.'
			));
		}

		$goal = Goal::curUser()->find($task->goal_id);
		if (!$goal) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
			));
		}

		/* Need start time for validate */
		$data['start_time'] = $timewatch->start_time;

		$this->timewatchStopValidator->with($data);

		if ($this->timewatchStopValidator->passes())
		{
			$timewatch->stop_time = $data['stop_time'];
			$timewatch->is_active = 0;

			if ($timewatch->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Timewatch stopped.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to stop timewatch.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->timewatchStopValidator->getErrors()
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
                $note = Note::curUser()->find($id);
                if (!$note)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Note not found.'
			));
                }

		$data = Input::get('note');

		$this->noteValidator->with($data);

		if ($this->noteValidator->passes())
		{
			/* Update data */
	                $note->title = $data['title'];
	                $note->pin_dashboard = $data['pin_dashboard'];
	                $note->pin_top = $data['pin_top'];
	                $note->note = $data['note'];

			if ($note->save())
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'Note updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update note.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->noteValidator->getErrors()
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
                $timewatch = Timewatch::curUser()->find($id);
                if (!$timewatch)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Timewatch not found.'
			));
                }

                if ($timewatch->delete())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Timewatch deleted.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete timewatch.'
			));
		}
	}
}
