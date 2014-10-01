<?php namespace Smartgoalz\Services\Validators;

class TimetableValidator extends Validator {

        public static $rules = array(
                'from_time' => 'required',
                'to_time' => 'required',
                'days' => 'required',
        );

}
