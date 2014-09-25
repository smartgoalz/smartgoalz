<?php namespace Smartgoalz\Gateways;

use Smartgoalz\Repositories\Note\NoteRepository;
use Smartgoalz\Services\Validators\NoteValidator;

class NoteGateway {

	protected $noteRepository;

	protected $noteValidator;

	public function __construct(NoteRepository $noteRepository, NoteValidator $noteValidator) {
		$this->noteRepository = $noteRepository;
		$this->noteValidator = $noteValidator;
	}

	public function getAll() {
		$data = $this->noteRepository->getAll();

		if ($data) {
			return array('status' => 'success', 'data' => array('notes' => $data));
		} else {
			return array('status' => 'error', 'message' => 'Failed to fetch notes.');
		}
	}

	public function get($id) {
		$data = $this->noteRepository->get($id);

		if ($data) {
			return array(
				'status' => 'success',
				'data' => array('note' => $data));
		} else {
			return array(
				'status' => 'error',
				'message' => 'Failed to fetch note.'
			);
		}
	}

	public function create($input) {

		$data = $input['note'];

		$this->noteValidator->with($data);

		if ($this->noteValidator->passes()) {
			if ($this->noteRepository->create($data)) {
				return array(
					'status' => 'success',
					'message' => 'Congratulations ! You have created a note.'
				);
			} else {
				return array(
					'status' => 'error',
					'message' => 'Oops ! Failed to create note.'
				);
			}
		} else {
			/* Return validation errors to the controller */
			return array(
				'status' => 'error',
				'message' => $this->noteValidator->getErrors()
			);
		}
	}

	public function update($id, $input) {

		$data = $input['note'];

		$this->noteValidator->with($data);

		if ($this->noteValidator->passes()) {
			if ($this->noteRepository->update($id, $data)) {
				return array(
					'status' => 'success',
					'message' => 'Great ! You have updated a note.'
				);
			} else {
				return array(
					'status' => 'error',
					'message' => 'Oops ! Failed to update note.'
				);
			}
		} else {
			/* Return validation errors to the controller */
			return array(
				'status' => 'error',
				'message' => $this->noteValidator->getErrors()
			);
		}
	}

	public function destroy($id) {
		if ($this->noteRepository->destroy($id)) {
			return array(
				'status' => 'success',
				'message' => 'You have deleted a note.'
			);
		} else {
			return array(
				'status' => 'error',
				'message' => 'Oops ! Failed to delete note.'
			);
		}
	}
}
