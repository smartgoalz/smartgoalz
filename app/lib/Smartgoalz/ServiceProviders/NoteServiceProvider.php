<?php namespace Smartgoalz\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Smartgoalz\Repositories\EloquentNoteRepository;

class NoteServiceProvider extends ServiceProvider {

	public function register()
	{
		/**
		 * Bind the Note repository interface to our Eloquent-specific implementation
		 * This service provider is called every time the application starts
		 */
		$this->app->bind(
			'Smartgoalz\Repositories\Note\NoteRepository',
			'Smartgoalz\Repositories\Note\EloquentNoteRepository'
		);
	}

}
