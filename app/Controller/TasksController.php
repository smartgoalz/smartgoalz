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

class TasksController extends AppController {

	public $components = array('RequestHandler');

	public function add() {
		$this->loadModel('Goal');

		$goal_id = $this->request->data['Task']['goal_id'];

		/* Check if valid goal */
		$count = $this->Goal->find('count', array(
			'conditions' => array(
				'Goal.id' => $goal_id,
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

		if ($this->Task->save($this->request->data)) {
			$message = array(
				'text' => __('Great ! You have added a task to your goal.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Task->validationErrors as $field => $msg) {
				$errmsg = $msg[0];
				break;
			}
			$message = array(
				'text' => $errmsg,
				'type' => 'error'
			);
		}

		$this->Goal->recalculateCompletion($goal_id);

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message'),
		));
		return;
	}

	public function edit($id) {
		$this->loadModel('Goal');

		$goal_id = $this->request->data['Task']['goal_id'];

		/* Check if valid goal */
		$count = $this->Goal->find('count', array(
			'conditions' => array(
				'Goal.id' => $goal_id,
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

		/* Read the task */
		$task = $this->Task->find('first', array('conditions' => array(
			'Task.id' => $id,
			'Task.goal_id' => $goal_id,
		)));
		if (!$task) {
			$message = array(
				'text' => __('Oops ! Task is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		$this->request->data['Task']['id'] = $id;

		if ($this->Task->save($this->request->data)) {
			$message = array(
				'text' => __('Cool ! Task has been updated.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Task->validationErrors as $field => $msg) {
				$errmsg = $msg[0];
				break;
			}
			$message = array(
				'text' => $errmsg,
				'type' => 'error'
			);
		}

		$this->Goal->recalculateCompletion($goal_id);

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
		return;
	}

	public function delete($id) {
		$this->loadModel('Goal');

		/* Read the task */
		$task = $this->Task->find('first', array('conditions' => array(
			'Task.id' => $id
		)));
		if (!$task) {
			$message = array(
				'text' => __('Oops ! Task is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		$goal_id = $task['Task']['goal_id'];

		/* Check if valid goal */
		$count = $this->Goal->find('count', array(
			'conditions' => array(
				'Goal.id' => $goal_id,
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

		if ($this->Task->delete($id)) {
			$message = array(
				'text' => __('Task has been deleted.'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Oops ! There was some error while deleting task. Please try again.'),
				'type' => 'error'
			);
		}

		$this->Goal->recalculateCompletion($goal_id);

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}

	public function done($id) {
		$this->loadModel('Goal');

		/* Read the task */
		$task = $this->Task->find('first', array('conditions' => array(
			'Task.id' => $id
		)));
		if (!$task) {
			$message = array(
				'text' => __('Oops ! Task is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		$goal_id = $task['Task']['goal_id'];

		/* Check if valid goal */
		$count = $this->Goal->find('count', array(
			'conditions' => array(
				'Goal.id' => $goal_id,
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

		/* Mark task as completed */
		$status = false;
		$this->Task->id = $id;
		if ($this->Task->saveField('is_completed', '1')) {
			$status = true;
			$message = array(
				'text' => __('Congratulations ! You have completed a task. You are one step closer to achieving your goal !'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Sorry ! There was some problem is marking the task as completed.'),
				'type' => 'error'
			);
		}

		$this->Goal->recalculateCompletion($goal_id);

		/* Check if goal is completed */
		$update_goal = $this->Goal->findById($goal_id);
		if ($status == true && $update_goal['Goal']['is_completed'] == true) {
			$message['text'] = __('Awesome ! You have completed your task and even achieved your SMART Goal !');
		}

		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}

	public function recalculate() {
		$message = array(
			'text' => __('OK'),
			'type' => 'success'
		);
		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}
}
