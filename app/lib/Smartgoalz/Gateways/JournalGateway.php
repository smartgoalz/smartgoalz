<?php namespace Smartgoalz\Gateways;

use Smartgoalz\Repositories\Journal\JournalRepository;
use Smartgoalz\Services\Validators\JournalValidator;

class JournalGateway {

	protected $journalRepository;

	protected $journalValidator;

	public function __construct(JournalRepository $journalRepository, JournalValidator $journalValidator) {
		$this->journalRepository = $journalRepository;
		$this->journalValidator = $journalValidator;
	}

	public function getAll() {
		$data = $this->journalRepository->getAll();

		if ($data) {
			return array('status' => 'success', 'data' => array('journals' => $data));
		} else {
			return array('status' => 'error', 'message' => 'Failed to fetch journal entries.');
		}
	}

	public function get($id) {
		$data = $this->journalRepository->get($id);

		if ($data) {
			return array(
				'status' => 'success',
				'data' => array('journal' => $data));
		} else {
			return array(
				'status' => 'error',
				'message' => 'Failed to fetch journal entry.'
			);
		}
	}

	public function create($input) {

		$data = $input['journal'];

		$this->journalValidator->with($data);

		if ($this->journalValidator->passes()) {
			if ($this->journalRepository->create($data)) {
				return array(
					'status' => 'success',
					'message' => 'Congratulations ! You have created a journal entry.'
				);
			} else {
				return array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create journal entry.'
				);
			}
		} else {
			/* Return validation errors to the controller */
			return array(
				'status' => 'error',
				'message' => $this->journalValidator->getErrors()
			);
		}
	}

	public function update($id, $input) {

		$data = $input['journal'];

		$this->journalValidator->with($data);

		if ($this->journalValidator->passes()) {
			if ($this->journalRepository->update($id, $data)) {
				return array(
					'status' => 'success',
					'message' => 'Great ! You have updated a journal entry.'
				);
			} else {
				return array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update journal entry.'
				);
			}
		} else {
			/* Return validation errors to the controller */
			return array(
				'status' => 'error',
				'message' => $this->journalValidator->getErrors()
			);
		}
	}

	public function destroy($id) {
		if ($this->journalRepository->destroy($id)) {
			return array(
				'status' => 'success',
				'message' => 'You have deleted a journal entry.'
			);
		} else {
			return array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete journal entry.'
			);
		}
	}
}
