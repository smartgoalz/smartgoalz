<?php namespace Smartgoalz\Services\Validators;

class JournalValidator extends Validator {

        public static $rules = array(
                'title' => 'required|max:255',
                'date' => 'required|date',
                'entry' => ''
        );

}
