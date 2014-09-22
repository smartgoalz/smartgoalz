<?php namespace Smartgoalz\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Smartgoalz\Repositories\EloquentJournalRepository;

class JournalServiceProvider extends ServiceProvider {

	public function register()
	{
		/**
		 * Bind the Journal repository interface to our Eloquent-specific implementation
		 * This service provider is called every time the application starts
		 */
		$this->app->bind(
			'Smartgoalz\Repositories\Journal\JournalRepository',
			'Smartgoalz\Repositories\Journal\EloquentJournalRepository'
		);
	}

}
