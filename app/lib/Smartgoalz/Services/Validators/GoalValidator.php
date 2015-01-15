<?php namespace Smartgoalz\Services\Validators;

class GoalValidator extends Validator {

        public static $rules = array(
                'category' => 'required',        /* category_id */
                'title' => 'required|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'difficulty' => 'required',
                'priority' => 'required'
        );

}
