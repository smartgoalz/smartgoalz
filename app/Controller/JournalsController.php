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

class JournalsController extends AppController {

	public $components = array('RequestHandler');

	public function index() {
		$journals = $this->Journal->find('all', array(
			'conditions' => array('Journal.user_id' => $this->Auth->user('id')),
			'order' => array('Journal.entrydate desc'),
		));
		$this->set(array(
			'journals' => $journals,
			'_serialize' => array('journals'),
		));
		return;
	}

	public function view($id) {
		$journal = $this->Journal->find('first', array(
			'conditions' => array(
				'Journal.id' => $id,
				'Journal.user_id' => $this->Auth->user('id'),
			),
		));

		$this->set(array(
			'journal' => $journal,
			'_serialize' => array('journal'),
		));
		return;
	}

	public function add() {
		$this->request->data['Journal']['user_id'] = $this->Auth->user('id');

		/* Add journal entry */
		$this->Journal->create();
		if ($this->Journal->save($this->request->data)) {
			$message = array(
				'text' => __('You have added a journal entry.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Journal->validationErrors as $field => $msg) {
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
		/* Check if journal entry is valid */
		$count = $this->Journal->find('count', array(
			'conditions' => array(
				'Journal.id' => $id,
				'Journal.user_id' => $this->Auth->user('id'),
			),
		));
		if ($count != 1) {
			$message = array(
				'text' => __('Oops ! Journal entry is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		/* Update note */
		$this->request->data['Journal']['user_id'] = $this->Auth->user('id');
		$this->request->data['Journal']['id'] = $id;

		if ($this->Journal->save($this->request->data)) {
			$message = array(
				'text' => __('Cool ! Journal entry has been updated.'),
				'type' => 'success'
			);
		} else {
			$errmsg = '';
			foreach ($this->Journal->validationErrors as $field => $msg) {
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
		/* Check if journal entry is valid */
		$count = $this->Journal->find('count', array(
			'conditions' => array(
				'Journal.id' => $id,
				'Journal.user_id' => $this->Auth->user('id'),
			),
		));
		if ($count != 1) {
			$message = array(
				'text' => __('Oops ! Journal entry is not valid. Please try again.'),
				'type' => 'error'
			);
			$this->set(array(
				'message' => $message,
				'_serialize' => array('message')
			));
			return;
		}

		/* Delete journal entry */
		if ($this->Journal->delete($id, true)) {
			$message = array(
				'text' => __('Journal entry has been deleted.'),
				'type' => 'success'
			);
		} else {
			$message = array(
				'text' => __('Oops ! There was some error while deleting the journal entry. Please try again.'),
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
