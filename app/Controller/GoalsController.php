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

class GoalsController extends AppController {

	public $components = array('RequestHandler');

	public function index() {
		$goals = $this->Goal->find('all', array(
			'conditions' => array('Goal.user_id' => $this->Auth->user('id')),
		));
		$this->set(array(
			'goals' => $goals,
			'_serialize' => array('goals'),
		));
		return;
	}

	public function view($id) {
		$goal = $this->Goal->find('first', array(
			'conditions' => array(
				'Goal.id' => $id,
				'Goal.user_id' => $this->Auth->user('id'),
			),
		));

		$this->set(array(
			'goal' => $goal,
			'_serialize' => array('goal'),
		));
		return;
	}

	public function add() {

		$this->request->data['Goal']['user_id'] = $this->Auth->user('id');

		/* Add goal */
		$this->Goal->create();
		if ($this->Goal->save($this->request->data)) {
			$message = array(
				'text' => __('Congratulations ! You have added a new goal.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Goal->validationErrors as $field => $msg) {
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
		/* Check if goal is valid */
		$count = $this->Goal->find('count', array(
			'conditions' => array(
				'Goal.id' => $id,
				'Goal.user_id' => $this->Auth->user('id'),
			),
		));
		if ($count != 1) {
			$message = array(
				'text' => __('Oops ! Goal is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		/* Update goal */
		$this->request->data['Goal']['user_id'] = $this->Auth->user('id');
		$this->request->data['Goal']['id'] = $id;

		if ($this->Goal->save($this->request->data)) {
			$message = array(
				'text' => __('Cool ! Goal has been updated.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Goal->validationErrors as $field => $msg) {
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
		/* Check if goal is valid */
		$count = $this->Goal->find('count', array(
			'conditions' => array(
				'Goal.id' => $id,
				'Goal.user_id' => $this->Auth->user('id'),
			),
		));
		if ($count != 1) {
			$message = array(
				'text' => __('Oops ! Goal is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		/* Delete goal */
		if ($this->Goal->delete($id, true)) {
			$message = array(
				'text' => __('Goal has been deleted.'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Oops ! There was some error while delete the goal. Please try again.'),
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
