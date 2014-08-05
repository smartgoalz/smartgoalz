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
		$goals = $this->Goal->find('all');
		$this->set(array(
			'goals' => $goals,
			'_serialize' => array('goals'),
		));
	}

	public function view($id) {
		$goal = $this->Goal->findById($id);
		$this->set(array(
			'goal' => $goal,
			'_serialize' => array('goal'),
		));
	}


	public function add() {
		if ($this->Goal->save($this->request->data)) {
			$message = array(
				'text' => __('Saved'),
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
	}

	public function edit($id) {
		$this->Goal->id = $id;
		if ($this->Goal->save($this->request->data)) {
			$message = array(
				'text' => __('Saved'),
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
	}

	public function delete($id) {
		if ($this->Goal->delete($id)) {
			$message = array(
				'text' => __('Goal deleted'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Error deleting goal'),
				'type' => 'error'
			);
		}
		$this->set(array(
			'message' => $message,
			'_serialize' => array('message')
		));
	}
}
