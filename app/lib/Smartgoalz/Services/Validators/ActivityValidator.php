<?php namespace Smartgoalz\Services\Validators;

class ActivityValidator extends Validator {

        public static $rules = array(
                'name' => 'required|max:255'
        );

}
