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

use Smartgoalz\Services\Validators\UserValidator;

class UsersController extends BaseController
{

	protected $userValidator;

	public function __construct(UserValidator $userValidator)
	{
		$this->userValidator = $userValidator;
	}

	public function getIndex()
	{
		return Redirect::action('UsersController@getLogin');
	}

	public function getLogin()
	{
		return View::make('users.login');
	}

	public function postLogin()
	{
		$input = Input::all();

		$login_data = array(
			'username' => $input['username'],
			'password' => $input['password'],
		);

		if (Auth::attempt($login_data))
		{
			/* Update last login datetime */
			$user = User::find(Auth::id());
			$user->last_login = date('Y-m-d H:i:s', time());
			$user->reset_password_key = NULL;
			$user->reset_password_date = NULL;
			$user->save();

			$user_info = '';
			if (strlen(Auth::user()->fullname) > 0)
			{
				$user_info = Auth::user()->fullname;
			}
			else
			{
				$user_info = Auth::user()->username;
			}

			return Redirect::intended('dashboard')
				->with('alert-success', 'Hi ' . $user_info . ', welcome back !');
		}

		return Redirect::action('UsersController@getLogin')
			->with('alert-danger', 'Login failed.');
	}

	public function getLogout()
	{
		Auth::logout();
		Session::flush();

                return Redirect::action('UsersController@getLogin')
                        ->with('alert-success', 'You have logged out successfully.');
	}

	public function getRegister()
	{
		return View::make('users.register');
	}

	public function postRegister()
	{
                $input = Input::all();

		$this->userValidator->with($input);

		if ($this->userValidator->fails())
		{
			return Redirect::back()->withInput()->withErrors($this->userValidator->getErrors());
		}
		else
		{
                        $user_data = array(
                                'username' => $input['username'],
				'password' => Hash::make($input['password']),
				'fullname' => '',
				'email' => $input['email'],
				'dateformat' => 'd-M-Y|dd-M-yy',
				'timezone' => 'UTC',
				'status' => 1,
				'verification_key' =>
					substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 20),
				'email_verified' => 0,
				'admin_verified' => 0,
				'retry_count' => 0,
				'reset_password_key' => NULL,
				'reset_password_date' => NULL,
                        );
                        $user = User::create($user_data);
			if (!$user)
			{
			        return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to create user.');
			}

			/* Send email on successful registration */
			try
			{
				Mail::send('emails.users.register', Input::all(), function($message) {
					$message
						->to(Input::get('email'), Input::get('username'))
						->subject('Welcome to ' . Config::get('smartgoalz.SITE_NAME') .
							' - Your account has been created'
						);
				});
			}
			catch (Exception $e)
			{
	                        return Redirect::action('UsersController@getLogin')
					->with('alert-success', 'User created. Please login below.')
					->with('alert-danger', 'Error sending email.');
			}

                        return Redirect::action('UsersController@getLogin')
                                ->with('alert-success', 'User created. Please login below.');
                }
	}

	public function getForgot()
	{
		return View::make('users.forgot');
	}

}
