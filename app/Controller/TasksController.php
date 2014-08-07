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

	public function index() {
		$tasks = $this->Task->find('all');
		$this->set(array(
			'tasks' => $tasks,
			'_serialize' => array('tasks'),
		));
	}

	public function view($id) {
		$task = $this->Task->findById($id);
		$this->set(array(
			'task' => $task,
			'_serialize' => array('task'),
		));
	}


	public function add() {
		if ($this->Task->save($this->request->data)) {
			$message = array(
				'text' => __('Saved'),
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
		$this->set(array(
			'message' => $message,
			'_serialize' => array('message'),
		));
	}

	public function edit($id) {
		$this->Task->id = $id;
		if ($this->Task->save($this->request->data)) {
			$message = array(
				'text' => __('Saved'),
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
		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}

	public function delete($id) {
		if ($this->Task->delete($id)) {
			$message = array(
				'text' => __('Task deleted'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Error deleting task'),
				'type' => 'error'
			);
		}
		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}

	public function done($id) {
		$this->Task->id = $id;
		if ($this->Task->saveField('is_completed', '1')) {
			$message = array(
				'text' => __('Task marked as completed'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Error in marking task as completed'),
				'type' => 'error'
			);
		}
		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}
}
