<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::group(array('prefix' => 'api'), function()
{
	Route::controller('goals', 'GoalsController');
	Route::controller('tasks', 'TasksController');
	Route::controller('categories', 'CategoriesController');
	Route::controller('notes', 'NotesController');
	Route::controller('journals', 'JournalsController');
});
