<?php

class DashboardController extends BaseController
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		/* Active Goals */
		$data['active_goals'] = Goal::curUser()->where('is_completed', '=', 0)->count();

		$data['pending_tasks'] = Task::withGoals()->where('tasks.is_completed', '=', 0)->count();

		$data['active_timewatches'] = Timewatch::curUser()->where('is_active', '=', 1)->count();

		$data['notes_dashboard'] = Note::curUser()->where('pin_dashboard', '=', 1)->get();

		return Response::json(array(
			'status' => 'success',
			'data' => array('dashboard' => $data)
		));
	}
}
