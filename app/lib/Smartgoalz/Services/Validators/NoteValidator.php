<?php namespace Smartgoalz\Services\Validators;

class NoteValidator extends Validator {

        public static $rules = array(
                'title' => 'required|max:255',
                'pin_dashboard' => 'required',
                'pin_top' => 'required',
                'note' => ''
        );

}
