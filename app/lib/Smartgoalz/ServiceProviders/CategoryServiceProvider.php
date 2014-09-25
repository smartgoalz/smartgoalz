<?php namespace Smartgoalz\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Smartgoalz\Repositories\EloquentCategoryRepository;

class CategoryServiceProvider extends ServiceProvider {

	public function register()
	{
		/**
		 * Bind the Category repository interface to our Eloquent-specific implementation
		 * This service provider is called every time the application starts
		 */
		$this->app->bind(
			'Smartgoalz\Repositories\Category\CategoryRepository',
			'Smartgoalz\Repositories\Category\EloquentCategoryRepository'
		);
	}

}
