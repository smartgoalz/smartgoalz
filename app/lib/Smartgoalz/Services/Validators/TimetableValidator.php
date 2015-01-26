<?php namespace Smartgoalz\Services\Validators;

class TimetableValidator extends Validator {

        public static $rules = array(
                'from_time' => 'required|date_format:H:i:s',
                'to_time' => 'required|date_format:H:i:s',
                'days' => 'required',
        );

}
