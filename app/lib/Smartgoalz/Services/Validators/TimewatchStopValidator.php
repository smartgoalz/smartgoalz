<?php namespace Smartgoalz\Services\Validators;

class TimewatchStopValidator extends Validator {

        public static $rules = array(
                'stop_time' => 'required|beforeTime:start_time|date'
        );

}
