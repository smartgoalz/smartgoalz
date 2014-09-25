<?php namespace Smartgoalz\Gateways;

use Smartgoalz\Repositories\Category\CategoryRepository;
use Smartgoalz\Services\Validators\CategoryValidator;

class CategoryGateway {

	protected $categoryRepository;

	protected $categoryValidator;

	public function __construct(CategoryRepository $categoryRepository, CategoryValidator $categoryValidator) {
		$this->categoryRepository = $categoryRepository;
		$this->categoryValidator = $categoryValidator;
	}

	public function getAll() {
		$data = $this->categoryRepository->getAll();

		if ($data) {
			return array('status' => 'success', 'data' => array('categories' => $data));
		} else {
			return array('status' => 'error', 'message' => 'Failed to fetch categories.');
		}
	}

	public function get($id) {
		$data = $this->categoryRepository->get($id);

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
