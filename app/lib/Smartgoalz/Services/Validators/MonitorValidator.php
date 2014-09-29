<?php namespace Smartgoalz\Services\Validators;

class MonitorValidator extends Validator
{

        public static $rules = array(
                'title' => 'required|max:255',
                'type' => 'required',
                'minimum' => 'required',
                'maximum' => 'required',
                'minimum_threshold' => 'required',
                'maximum_threshold' => 'required',
                'is_lower_better' => 'required',
                'units' => 'required',
                'frequency' => 'required',
                'description' => 'max:255',
        );

}
