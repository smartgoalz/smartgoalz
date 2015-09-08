<?php
/**
 * The MIT License (MIT)
 *
 * SMARTGoalz - SMART Goals made easier
 *
 * http://smartgoalz.github.io
 *
 * Copyright (c) 2015 Prashant Shah <pshah.smartgoalz@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Smartgoalz\Services\Validators\CategoryValidator;

class CategoriesController extends BaseController
{

	protected $categoryValidator;

	public function __construct(CategoryValidator $categoryValidator)
	{
		$this->categoryValidator = $categoryValidator;
	}

	public function getIndex()
	{
		$categories = Category::curUser()->orderBy('title', 'DESC')->get();

		if (!$categories)
		{
			return Redirect::action('SettingsController@getIndex')
				->with('alert-danger', 'Categories not found.');
		}

		return View::make('categories.index')
			->with('categories', $categories);
	}

	public function getCreate()
	{
		return View::make('categories.create');
	}

	public function postCreate()
	{
		$input = Input::all();

		/* Check if category title is unique for a user */
		$categories = Category::curUser()->like('title', $input['title']);

		if ($categories->count() >= 1)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Category with same name already exists.');
		}

		$this->categoryValidator->with($input);

		if ($this->categoryValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->categoryValidator->getErrors());
		}
		else
		{
			if (!Category::create($input))
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create goal category.');
			}

                        return Redirect::action('CategoriesController@getIndex')
                                ->with('alert-success', 'Goal category created.');
		}
	}

	public function getEdit($id)
	{
		$category = Category::curUser()->find($id);

		if (!$category)
		{
			return Redirect::action('CategoriesController@getIndex')
				->with('alert-danger', 'Goal category not found.');
		}

		return View::make('categories.edit')
			->with('category', $category);
	}

	public function postEdit($id)
	{
		$category = Category::curUser()->find($id);

		if (!$category)
		{
			return Redirect::action('CategoriesController@getIndex')
				->with('alert-danger', 'Goal category not found.');
		}

		$input = Input::all();

		/* Check if category title is unique for a user */
		$categories = Category::curUser()->like('title', $input['title'])
			->where('id', '!=', $id);

		if ($categories->count() >= 1)
		{
			return Redirect::back()->withInput()
				->with('alert-danger', 'Category with same name already exists.');
		}

		$this->categoryValidator->with($input);

		if ($this->categoryValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->categoryValidator->getErrors());
		}
		else
		{
			/* Update data */
			$category->title = $input['title'];

			if (!$category->save())
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update goal category.');
			}

                        return Redirect::action('CategoriesController@getIndex')
                                ->with('alert-success', 'Goal category updated.');
		}
	}

	public function deleteDestroy($id)
	{
                $category = Category::curUser()->find($id);
		if (!$category)
		{
			return Redirect::action('CategoriesController@getIndex')
				->with('alert-danger', 'Goal category not found.');
                }

		if ($category->goals->count() >= 1)
		{
			return Redirect::action('CategoriesController@getIndex')
				->with('alert-danger', 'Failed to delete goal category since there are still goals under this category.');
		}

                if (!$category->delete())
		{
			return Redirect::action('CategoriesController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete goal category.');
		}

		return Redirect::action('CategoriesController@getIndex')
			->with('alert-success', 'Goal category deleted.');
	}

}
