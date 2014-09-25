<?php namespace Smartgoalz\Services\Validators;

class CategoryValidator extends Validator {

        public static $rules = array(
                'title' => 'required|max:255'
        );

}
