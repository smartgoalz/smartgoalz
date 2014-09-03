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
			'dependent' => true,
			'exclusive' => true,
			'order' => 'Task.weight ASC'
		),
	);

	/* Validation rules for the Group table */
	public $validate = array(
		'title' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Oops ! Goal title cannot be empty.',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'checkUnique',
				'message' => 'Arrg ! Goal title is already in use.',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Oh ! Goal title cannot be more than 255 characters.',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'category_id' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Oops ! Category cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'categoryExists',
				'message' => 'Arrg ! You have selected a invalid category.',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'difficulty' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Oops ! Difficulty cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('1', '2', '3', '4', '5')),
				'message' => 'Arrg ! Difficulty is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
		'priority' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Oops ! Priority cannot be empty',
				'required' => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => array('inList', array('1', '2', '3', '4', '5')),
				'message' => 'Arrg ! Priority is not valid',
				'required' => true,
				'allowEmpty' => false,
			),
		),
	);

	/**
	 * Checks if goal title is unique for every user
	 */
	public function checkUnique($title) {
		if (empty($this->data[$this->alias]['id'])) {
			$count = $this->find('count', array(
				'conditions' => array(
					'Goal.title' => $title,
					'Goal.user_id' => $this->data[$this->alias]['user_id'],
				)
			));
		} else {
			$count = $this->find('count', array(
				'conditions' => array(
					'Goal.id !=' => $this->data[$this->alias]['id'],
					'Goal.title' => $title,
					'Goal.user_id' => $this->data[$this->alias]['user_id'],
				)
			));
		}
		return $count == 0;
	}

	/**
	 * Checks if category exists for the user
	 */
	public function categoryExists($id) {
		$count = $this->Category->find('count', array(
			'conditions' => array(
				'Category.id' => $this->data[$this->alias]['category_id'],
				'Category.user_id' => $this->data[$this->alias]['user_id'],
			)
		));
		return $count != 0;
	}

	/**
	 * Recalculate the completion count, and check if goal is completed
	 */
	public function recalculateCompletion($id) {
		$goal = $this->findById($id);

		if (empty($goal)) {
			return false;
		}

		$task_total = 0;
		$task_completed = 0;
		foreach ($goal['Task'] as $row => $task) {
			if ($task['is_completed'] == true) {
				$task_completed++;
			}
			$task_total++;
		}
		$this->id = $goal['Goal']['id'];
		if (!$this->saveField('task_total', $task_total)) {
			return false;
		}
		$this->id = $goal['Goal']['id'];
		if (!$this->saveField('task_completed', $task_completed)) {
			return false;
		}

		$completed = false;
		if ($task_total == $task_completed) {
			$completed = true;
		}
		if ($task_total == 0) {
			$completed = false;
		}

		if ($completed) {
			$this->id = $goal['Goal']['id'];
			if (!$this->saveField('is_completed', 1)) {
				return false;
			}
		} else {
			/* Completed 100% */
			$this->id = $goal['Goal']['id'];
			if (!$this->saveField('is_completed', 0)) {
				return false;
			}
		}
		return true;
	}
}
