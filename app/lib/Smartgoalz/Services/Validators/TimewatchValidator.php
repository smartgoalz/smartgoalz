<?php namespace Smartgoalz\Services\Validators;

class TimewatchValidator extends Validator {

        public static $rules = array(
                'start_time' => 'required|date',
                'stop_time' => 'beforeTime:start_time|date',
                'is_active' => 'required',
        );

}
