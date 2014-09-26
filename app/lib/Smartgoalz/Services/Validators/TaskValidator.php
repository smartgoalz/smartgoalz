<?php namespace Smartgoalz\Services\Validators;

class TaskValidator extends Validator {

        public static $rules = array(
                'title' => 'required|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'is_completed' => 'required',
                'completion_date' => 'date',
                'notes' => ''
        );

}
