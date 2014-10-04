<?php namespace Smartgoalz\Services\Validators;

class UserValidator extends Validator {

        public static $rules = array(
                'username' => 'required|max:255|unique:users,username',
                'password' => 'required',
                'email' => 'required|email|unique:users,email',
        );

}
