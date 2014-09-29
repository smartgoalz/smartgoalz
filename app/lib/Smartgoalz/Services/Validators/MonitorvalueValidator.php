<?php namespace Smartgoalz\Services\Validators;

class MonitorvalueValidator extends Validator
{

        public static $rules = array(
                'value' => 'required|max:15',
                'date' => 'required|date',
        );

}
