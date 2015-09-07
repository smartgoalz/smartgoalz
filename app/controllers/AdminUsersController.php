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

class AdminUsersController extends BaseController
{
	public function getIndex()
	{
		$users = User::orderBy('username', 'DESC')->get();

		if (!$users)
		{
			return Redirect::action('DashboardController@getIndex')
				->with('alert-danger', 'Users not found.');
		}

		return View::make('admin.users.index')
			->with('users', $users);
	}

	public function getCreate()
	{
		$timezone_options = array('' => 'Please select...') + timezone_list();

		$gender_options = array(
			'' => 'Please select...',
			'M' => 'Male',
			'F' => 'Female',
			'U' => 'Undisclosed',
		);

		$dateformat_options = array(
	                '' => 'Please select...',
	                'd-M-Y|dd-M-yy' => 'Day-Month-Year',
	                'M-d-Y|M-dd-yy' => 'Month-Day-Year',
	                'Y-M-d|yy-M-dd' => 'Year-Month-Day',
		);

		$status_options = array(
			'' => 'Please select...',
			'0' => 'Inactive',
			'1' => 'Active',
		);

		return View::make('admin.users.create')
			->with('timezone_options', $timezone_options)
			->with('gender_options', $gender_options)
			->with('dateformat_options', $dateformat_options)
			->with('status_options', $status_options);
	}

	public function postCreate()
	{
                $input = Input::all();

		$php_dateformat = explode('|', $input['dateformat'])[0];
                $temp = date_create_from_format($php_dateformat, $input['dob']);
		if (!$temp)
		{
	                return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid date of birth.');
		}

		if (!isset($input['is_admin']))
		{
			$input['is_admin'] = 0;
		}
		if (!isset($input['email_verified']))
		{
			$input['email_verified'] = 0;
		}
		if (!isset($input['admin_verified']))
		{
			$input['admin_verified'] = 0;
		}

                $rules = array(
			'username' => 'required|min:1|max:255|unique:users,username',
			'fullname' => 'required|min:1|max:255',
			'email' => 'required|email|unique:users,email',
			'dob' => 'required|date',
			'gender' => 'required|in:M,F,U',
			'dateformat' => 'required',
			'timezone' => 'required',
			'is_admin' => 'required|in:0,1',
			'email_verified' => 'required|in:0,1',
			'admin_verified' => 'required|in:0,1',
			'status' => 'required|in:0,1',
                );

                $validator = Validator::make($input, $rules);

                if ($validator->fails())
                {
                        return Redirect::back()->withInput()->withErrors($validator);
                }
		else
		{
                        /* Create user */
                        $user_data = array(
                                'username' => $input['username'],
				'password' => Hash::make($input['password']),
				'fullname' => $input['fullname'],
				'email' => $input['email'],
				'dob' => date_format($temp, 'Y-m-d'),
				'gender' => $input['gender'],
				'dateformat' => $input['dateformat'],
				'timezone' => $input['timezone'],
				'is_admin' => $input['is_admin'],
				'email_verified' => $input['email_verified'],
				'admin_verified' => $input['admin_verified'],
				'status' => $input['status'],
				'retry_count' => 0,
				'last_login' => NULL,
				'reset_password_key' => NULL,
				'reset_password_date' => NULL,
                        );
                        $user = User::create($user_data);
			if (!$user)
			{
				return Redirect::back()->withInput()
					->with('alert-danger', 'Failed to create user.');
			}

			/* Since these are gaurded fields that cannot be mass assigned, doing it this way */
			$user->is_admin = $input['is_admin'];
			$user->email_verified = $input['email_verified'];
			$user->admin_verified = $input['admin_verified'];
			$user->status = $input['status'];

                        if (!$user->save())
                        {
				return Redirect::action('AdminUsersController@getIndex')
                                        ->with('alert-danger', 'Failed to update user profile.');
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
	                        return Redirect::action('AdminUsersController@getIndex')
					->with('alert-success', 'User created.')
					->with('alert-danger', 'Error sending email.');
			}

                        return Redirect::action('AdminUsersController@getIndex')
                                ->with('alert-success', 'User created.');
                }
	}

	public function getEdit($id)
	{
		$user = User::find($id);

		if (!$user)
		{
			return Redirect::action('AdminUsersController@getIndex')
				->with('alert-danger', 'User not found.');
		}

		$timezone_options = array('' => 'Please select...') + timezone_list();

		$gender_options = array(
			'' => 'Please select...',
			'M' => 'Male',
			'F' => 'Female',
			'U' => 'Undisclosed',
		);

		$dateformat_options = array(
	                '' => 'Please select...',
	                'd-M-Y|dd-M-yy' => 'Day-Month-Year',
	                'M-d-Y|M-dd-yy' => 'Month-Day-Year',
	                'Y-M-d|yy-M-dd' => 'Year-Month-Day',
		);

		$status_options = array(
			'' => 'Please select...',
			'0' => 'Inactive',
			'1' => 'Active',
		);

		$dob = '';
                $temp = date_create_from_format(
			'Y-m-d', $user->dob
		);
		if ($temp)
		{
			$dob = date_format(
				$temp, explode('|', $user->dateformat)[0]
			);
		}

		return View::make('admin.users.edit')
			->with('dob', $dob)
			->with('timezone_options', $timezone_options)
			->with('gender_options', $gender_options)
			->with('dateformat_options', $dateformat_options)
			->with('status_options', $status_options)
			->with('user', $user);
	}

	public function postEdit($id)
	{
		$user = User::find($id);

		if (!$user)
		{
			return Redirect::action('AdminUsersController@getIndex')
				->with('alert-danger', 'User not found.');
		}

                $input = Input::all();

		$php_dateformat = explode('|', $input['dateformat'])[0];
                $temp = date_create_from_format($php_dateformat, $input['dob']);
		if (!$temp)
		{
	                return Redirect::back()->withInput()
                                ->with('alert-danger', 'Invalid date of birth.');
		}

		if (!isset($input['is_admin']))
		{
			$input['is_admin'] = 0;
		}
		if (!isset($input['email_verified']))
		{
			$input['email_verified'] = 0;
		}
		if (!isset($input['admin_verified']))
		{
			$input['admin_verified'] = 0;
		}

                $rules = array(
			'username' => 'required|min:1|max:255|unique:users,username,'.$user->id,
			'fullname' => 'required|min:1|max:255',
			'email' => 'required|email|unique:users,email,'.$user->id,
			'dob' => 'required|date',
			'gender' => 'required|in:M,F,U',
			'dateformat' => 'required',
			'timezone' => 'required',
			'is_admin' => 'required|in:0,1',
			'email_verified' => 'required|in:0,1',
			'admin_verified' => 'required|in:0,1',
			'status' => 'required|in:0,1',
                );

                $validator = Validator::make($input, $rules);

                if ($validator->fails())
                {
                        return Redirect::back()->withInput()->withErrors($validator);
                }
		else
		{
                        /* Update user */
                        $user->fullname = $input['fullname'];
                        $user->email = $input['email'];
			$user->dob = date_format($temp, 'Y-m-d');
                        $user->gender = $input['gender'];
			$user->dateformat = $input['dateformat'];
			$user->timezone = $input['timezone'];
			$user->is_admin = $input['is_admin'];
			$user->email_verified = $input['email_verified'];
			$user->admin_verified = $input['admin_verified'];
			$user->status = $input['status'];

                        if (!$user->save())
                        {
		                return Redirect::back()->withInput()
                                        ->with('alert-danger', 'Failed to update user profile.');
                        }

                        return Redirect::action('AdminUsersController@getIndex')
                                ->with('alert-success', 'User profile updated.');

		}
	}

	public function deleteDestroy($id)
	{
		$user = User::find($id);

		if (!$user)
		{
			return Redirect::action('AdminUsersController@getIndex')
				->with('alert-danger', 'User not found.');
		}

                if (!$user->delete())
		{
			return Redirect::action('AdminUsersController@getIndex')
				->with('alert-danger', 'Oops ! Failed to delete user.');
		}

		return Redirect::action('AdminUsersController@getIndex')
			->with('alert-success', 'User deleted.');
	}

}
