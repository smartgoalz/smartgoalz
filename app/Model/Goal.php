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
 * Goal Model
 */
class Goal extends AppModel {

	public $belongsTo = array(
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	public $hasMany = array(
		'Task' => array(
			'className' => 'Task',
		),
	);

	/* Validation rules for the Group table */
	public $validate = array(
		'title' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Goal title cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isUnique',
				'message' => 'Goal title is already in use',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Goal title cannot be more than 255 characters',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'difficulty' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Difficulty cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('1', '2', '3', '4', '5')),
				'message' => 'Difficulty is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'priority' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Priority cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10')),
				'message' => 'Priority is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);

	public function recalCompletion($id) {
		$goal = $this->findById($id);

		if (empty($goal)) {
			return false;
		}

		$total_count = 0;
		$total_done = 0;
		foreach ($goal['Task'] as $row => $task) {
			if ($task['is_completed'] == true) {
				$total_done++;
			}
			$total_count++;
		}
		if ($total_count != $total_done) {
			$percent_done = ($total_done * 100) / $total_count;
			$percent_done = (int)$percent_done;

			$this->id = $goal['Goal']['id'];
			if (!$this->saveField('completion_percent', $percent_done)) {
				return false;
			}
			if (!$this->saveField('is_completed', 0)) {
				return false;
			}
		} else {
			/* Completed 100% */
			$this->id = $goal['Goal']['id'];
			if (!$this->saveField('completion_percent', 100)) {
				return false;
			}
			if (!$this->saveField('is_completed', 1)) {
				return false;
			}
		}
		return true;
	}
}
