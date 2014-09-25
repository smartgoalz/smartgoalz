<?php namespace Smartgoalz\Gateways;

use Smartgoalz\Repositories\Goal\GoalRepository;
use Smartgoalz\Services\Validators\GoalValidator;

class GoalGateway {

	protected $goalRepository;

	protected $goalValidator;

	public function __construct(GoalRepository $goalRepository, GoalValidator $goalValidator) {
		$this->goalRepository = $goalRepository;
		$this->goalValidator = $goalValidator;
	}

	public function getAll() {
		$data = $this->goalRepository->getAll();

		if ($data) {
			return array('status' => 'success', 'data' => array('goals' => $data));
		} else {
			return array('status' => 'error', 'message' => 'Failed to fetch goals.');
		}
	}

	public function get($id) {
		$data = $this->goalRepository->get($id);

		if ($data) {
			return array(
				'status' => 'success',
				'data' => array('goal' => $data));
		} else {
			return array(
				'status' => 'error',
				'message' => 'Failed to fetch goal.'
			);
		}
	}

	public function create($input) {

		$data = $input['goal'];

		/* Set default values for a new goal */
		$data['is_completed'] = 0;
		$data['completion_date'] = NULL;
		$data['task_total'] = 0;
		$data['task_completed'] = 0;
		$data['status'] = 'ACTIVE';

		$this->goalValidator->with($data);

		if ($this->goalValidator->passes()) {
			if ($this->goalRepository->create($data)) {
				return array(
					'status' => 'success',
					'message' => 'Congratulations ! You have created a goal.'
				);
			} else {
				return array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create goal.'
				);
			}
		} else {
			/* Return validation errors to the controller */
			return array(
				'status' => 'error',
				'message' => $this->goalValidator->getErrors()
			);
		}
	}

	public function update($id, $input) {

		$data = $input['goal'];

		unset($data['is_completed']);
		unset($data['completion_date']);
		unset($data['task_total']);
		unset($data['task_completed']);

		$this->goalValidator->with($data);

		if ($this->goalValidator->passes()) {
			if ($this->goalRepository->update($id, $data)) {
				return array(
					'status' => 'success',
					'message' => 'Great ! You have updated a goal.'
				);
			} else {
				return array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update goal.'
				);
			}
		} else {
			/* Return validation errors to the controller */
			return array(
				'status' => 'error',
				'message' => $this->goalValidator->getErrors()
			);
		}
	}

	public function destroy($id) {
		if ($this->goalRepository->destroy($id)) {
			return array(
				'status' => 'success',
				'message' => 'You have deleted a goal.'
			);
		} else {
			return array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete goal.'
			);
		}
	}
}
