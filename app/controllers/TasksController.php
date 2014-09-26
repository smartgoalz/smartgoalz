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

		$data = Task::where('goal_id', $goal_id)->orderBy('weight', 'ASC')->get();

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
		$data = Input::get('task');

		$goal_id = $data['goal_id'];

		$goal = Goal::curUser()->find($goal_id);
		if (!$goal) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
			));
		}

		/* Calculate weight for current task */
		list($weight, $recalculate) = $this->calculateWeight($goal_id, NULL, $data['prev_id']);
		unset($data['prev_id']);
		$data['weight'] = $weight;

		if (empty($data['is_completed']))
		{
			$data['completion_date'] = NULL;
		}

		$this->taskValidator->with($data);

		if ($this->taskValidator->passes())
		{
			if (Task::create($data))
			{
				/* Recalculate weights of all the tasks if needed */
				if ($recalculate) {
					Task::recalculateWeights($goal_id);
				}

				return Response::json(array(
					'status' => 'success',
					'message' => 'New task added to goal.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to add task to goal.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->taskValidator->getErrors()
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
		$task = Task::find($id);
		if (!$task) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Task not found.'
			));
		}

		$data = Input::get('task');

		$goal_id = $task->goal_id;
		unset($data['goal_id']);

		$goal = Goal::curUser()->find($goal_id);
		if (!$goal) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Goal not found.'
			));
		}

		/* Calculate weight */
		list($weight, $recalculate) = $this->calculateWeight($goal_id, $id, $data['prev_id']);
		unset($data['prev_id']);

		if (empty($data['is_completed']))
		{
			$data['completion_date'] = NULL;
		}

		$this->taskValidator->with($data);

		if ($this->taskValidator->passes())
		{
			$task->title = $data['title'];
			$task->start_date = $data['start_date'];
			$task->due_date = $data['due_date'];
			$task->is_completed = $data['is_completed'];
			$task->completion_date = $data['completion_date'];
			$task->notes = $data['notes'];
			$task->weight = $weight;

			if ($task->save())
			{
				/* Recalculate weights of all the tasks if needed */
				if ($recalculate) {
					Task::recalculateWeights($goal_id);
				}

				return Response::json(array(
					'status' => 'success',
					'message' => 'Task updated.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update task.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->taskValidator->getErrors()
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
	 * Mark the task as completed
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

	/**
	 * Calculate the weight to assign to current task.
	 *
	 * @param  int  $goal_id - Goal id
	 * @param  int  $cur_task_id - Current task id if present else NULL
	 * @param  int  $prev_id - Prev task id if present else 0
	 * @return int, boolean $weight, $recalculate - Calculated weight, If weight recalculation is required
	 */
	public function calculateWeight($goal_id, $cur_task_id, $prev_id = 0)
	{
		$step_size = 100000;
		$recalculate = false;

                $tasks = Task::where('goal_id', $goal_id)->orderBy('weight', 'ASC')->get();
                if (!$tasks)
		{
			return array($step_size, $recalculate);
                }

		/* if edit action, then cur_task_id is set, remove the task from the list */
		if ($cur_task_id) {
			foreach ($tasks as $row => $task) {
				if ($task->id == $cur_task_id) {
					unset($tasks[$row]);
					break;
				}
			}
		}

		/**** Find previous and next task ****/
		$prev_weight = -1;
		$next_weight = -1;

		/* If first task, then prev_weight is 0, calculate next_weight */
		if ($prev_id == 0)
		{
			$prev_weight = 0;
			/* Check if there are any other task after it */
			if (count($tasks) <= 0)
			{
				/* If no other task found then the weight is the step_size */
				return array($step_size, false);
			} else {
				$next_weight = $tasks[0]->weight;
			}
		} else {
			foreach ($tasks as $task) {
				if ($task->id == $prev_id) {
					$prev_weight = $task->weight;
					continue;
				}
				if ($prev_weight != -1) {
					$next_weight = $task->weight;
					break;
				}
			}
			/* If last task, then next_weight will be prev_weight + step_size  */
			if ($next_weight == -1) {
				return array($prev_weight + $step_size, $recalculate);
			}
		}

		/* Calculate weight */
		$inc = ($next_weight - $prev_weight) / 2;
		if ($inc <= 10) {
			/* Re-calculate all tasks weights */
			$recalculate = true;
		}
		$weight = $prev_weight + (($next_weight - $prev_weight) / 2);

		return array($weight, $recalculate);
	}
}
