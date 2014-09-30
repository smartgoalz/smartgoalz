<?php namespace Smartgoalz\Services\Validators;

class TimetableValidator extends Validator {

        public static $rules = array(
                'activity' => 'required|max:255',
                'from_time' => 'required',
                'to_time' => 'required',
                'days' => 'required',
                'track' => 'required'
        );

}
