<?php namespace Smartgoalz\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Smartgoalz\Repositories\EloquentGoalRepository;

class GoalServiceProvider extends ServiceProvider {

	public function register()
	{
		/**
		 * Bind the Goal repository interface to our Eloquent-specific implementation
		 * This service provider is called every time the application starts
		 */
		$this->app->bind(
			'Smartgoalz\Repositories\Goal\GoalRepository',
			'Smartgoalz\Repositories\Goal\EloquentGoalRepository'
		);
	}

}
