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
	Route::controller('dashboard', 'DashboardController');
	Route::controller('goals', 'GoalsController');
	Route::controller('tasks', 'TasksController');
	Route::controller('categories', 'CategoriesController');
	Route::controller('timewatches', 'TimewatchesController');
	Route::controller('timetables', 'TimetablesController');
	Route::controller('activities', 'ActivitiesController');
	Route::controller('monitors', 'MonitorsController');
	Route::controller('monitorvalues', 'MonitorvaluesController');
	Route::controller('notes', 'NotesController');
	Route::controller('journals', 'JournalsController');
	Route::controller('users', 'UsersController');
});
