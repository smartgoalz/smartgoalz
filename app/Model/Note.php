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

/**
 * Note Model
 */
class Note extends AppModel {

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	/* Validation rules for the Group table */
	public $validate = array(
		'title' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Oops ! Notes title cannot be empty.',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'checkUnique',
				'message' => 'Arrg ! Notes title is already in use.',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Oh ! Notes title cannot be more than 255 characters.',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
	);

	/**
	 * Checks if note title is unique for every user
	 */
	public function checkUnique($title) {
		if (empty($this->data[$this->alias]['id'])) {
			$count = $this->find('count', array(
				'conditions' => array(
					'Note.title' => $title,
					'Note.user_id' => $this->data[$this->alias]['user_id'],
				)
			));
		} else {
			$count = $this->find('count', array(
				'conditions' => array(
					'Note.id !=' => $this->data[$this->alias]['id'],
					'Note.title' => $title,
					'Note.user_id' => $this->data[$this->alias]['user_id'],
				)
			));
		}
		return $count == 0;
	}
}
