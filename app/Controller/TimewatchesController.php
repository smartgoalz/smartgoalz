<?php
/**
 * The MIT License (MIT)
 *
 * S.M.A.R.T. Goalz
 *
 * Copyright (c) 2014 Prashant Shah <pshah.me@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

App::uses('AppController', 'Controller');

class TimewatchesController extends AppController {

	public $components = array('RequestHandler');

	public function index() {
		$this->loadModel('Task');

		$timewatches = $this->Timewatch->find('all', array(
			'conditions' => array('Timewatch.user_id' => $this->Auth->user('id')),
			'joins' => array(
				array(
					'table' => 'tasks',
					'alias' => 'TaskList',
					'type' => 'LEFT',
					'conditions' => array(
						'Timewatch.task_id = TaskList.id',
					),
				),
			),
			'order' => array('Timewatch.is_active desc'),
		));

		$this->set(array(
			'timewatches' => $timewatches,
			'_serialize' => array('timewatches'),
		));
		return;
	}

	public function view($id) {
		$this->loadModel('Goal');

		$timewatch = $this->Timewatch->findById($id);

		$goal = $this->Goal->find('first', array('conditions' => array(
			'Goal.id' => $timewatch['Task']['goal_id'],
			'Goal.user_id' => $this->Auth->user('id'),
		)));

		$timewatch['Goal'] = $goal['Goal'];

		$this->set(array(
			'timewatch' => $timewatch,
			'_serialize' => array('timewatch'),
		));
		return;
	}

	public function start() {

		/* TODO : Validate */

		$this->request->data['Timewatch']['is_active'] = TRUE;
		$this->request->data['Timewatch']['user_id'] = $this->Auth->user('id');

		/* Add timwatch */
		$this->Timewatch->create();
		if ($this->Timewatch->save($this->request->data)) {
			$message = array(
				'text' => __('You have started a task. Feel free to navigate away from this page, when you come back you can stop the timer.'),
				'type' => 'success',
				'id' => $this->Timewatch->id,
			);
		} else {
			$errmsg = '';
			foreach ($this->Note->validationErrors as $field => $msg) {
				$errmsg = $msg[0];
				break;
			}
			$message = array(
				'text' => $errmsg,
				'type' => 'error'
			);
		}

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message'),
		));
		return;
	}

	public function stop($id) {

		/* TODO : Validate */
		$timewatch = $this->Timewatch->findById($id);

		if ($timewatch['Timewatch']['is_active'] == 0) {
			$message = array(
				'text' => __('Oops ! Timer for the selected task is already stopped.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message'),
			));
			return;
		}

		$this->request->data['Timewatch']['is_active'] = 0;
		$this->request->data['Timewatch']['id'] = $id;
		unset($this->request->data['Timewatch']['task_id']);
		unset($this->request->data['Timewatch']['start_time']);

		$this->Timewatch->id = $id;

		/* Stop timwatch */
		if ($this->Timewatch->save($this->request->data)) {
			$message = array(
				'text' => __('Timer stopped. You have spend %d hours on it.', 10),
				'type' => 'success',
			);
		} else {
			$errmsg = '';
			foreach ($this->Note->validationErrors as $field => $msg) {
				$errmsg = $msg[0];
				break;
			}
			$message = array(
				'text' => $errmsg,
				'type' => 'error'
			);
		}

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message'),
		));
		return;
	}

	public function edit($id) {
		/* Check if timewatch is valid */
		$count = $this->Timewatch->find('count', array(
			'conditions' => array(
				'Timewatch.id' => $id,
				'Timewatch.user_id' => $this->Auth->user('id'),
			),
		));
		if ($count != 1) {
			$message = array(
				'text' => __('Oops ! Timewatch is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		/* Update timewatch */
		$this->request->data['Timewatch']['user_id'] = $this->Auth->user('id');
		$this->request->data['Timewatch']['id'] = $id;

		if ($this->Timewatch->save($this->request->data)) {
			$message = array(
				'text' => __('Cool ! Timewatch has been updated.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Note->validationErrors as $field => $msg) {
				$errmsg = $msg[0];
				break;
			}
			$message = array(
				'text' => $errmsg,
				'type' => 'error'
			);
		}

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
		return;
	}

	public function delete($id) {
		/* Check if timewatch is valid */
		$count = $this->Timewatch->find('count', array(
			'conditions' => array(
				'Timewatch.id' => $id,
				'Timewatch.user_id' => $this->Auth->user('id'),
			),
		));
		if ($count != 1) {
			$message = array(
				'text' => __('Oops ! Timewatch is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		/* Delete timewatch */
		if ($this->Timewatch->delete($id)) {
			$message = array(
				'text' => __('Timewatch has been deleted.'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Oops ! There was some error while delete the timewatch. Please try again.'),
				'type' => 'error'
			);
		}

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
		return;
	}
}
