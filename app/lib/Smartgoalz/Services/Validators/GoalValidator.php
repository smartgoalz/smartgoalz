<?php namespace Smartgoalz\Services\Validators;

class GoalValidator extends Validator {

        public static $rules = array(
                'category_id' => 'required',
                'title' => 'required|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'difficulty' => 'required',
                'priority' => 'required'
        );

}
