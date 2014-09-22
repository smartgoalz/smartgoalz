<?php namespace Smartgoalz\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Smartgoalz\Extensions\Validation\CustomValidator;

class ValidationServiceProvider extends ServiceProvider {

        public function register() {}

        public function boot()
        {
                $this->app->validator->resolver(function($translator, $data, $rules, $messages)
                {
                        return new CustomValidator($translator, $data, $rules, $messages);
                });
        }

}
