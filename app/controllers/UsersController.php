<?php

use Smartgoalz\Services\Validators\UserValidator;

class UsersController extends BaseController
{

	protected $userValidator;

	public function __construct(UserValidator $userValidator)
	{
		$this->userValidator = $userValidator;
	}

	/**
	 * Login user
	 *
	 * @return Response
	 */
	public function postLogin()
	{
		$data = Input::get('user');

		if (empty($data['username'])) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Username is required.'
			));
		}
		if (empty($data['password'])) {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Passowrd is required.'
			));
		}

		$remember_me = false;
		if ($data['remember_me'] == TRUE) {
			$remember_me = true;
		} else {
			$remember_me = false;
		}

		if (Auth::attempt(array(
				'username' => $data['username'],
				'password' => $data['password']
			),
			$remember_me))
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('user' => array())
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Login failed.'
			));
		}

	}

	/**
	 * Logout user
	 *
	 * @return Response
	 */
	public function getLogout()
	{
		Auth::logout();

		return Response::json(array(
			'status' => 'success',
			'data' => array('user' => array())
		));
	}

	/**
	 * Regsiter user
	 *
	 * @return Response
	 */
	public function postRegister()
	{
		$input = Input::get('user');

		$data['username'] = $input['username'];
		$data['password'] = Hash::make($input['password']);
		$data['email'] = $input['email'];

		$this->userValidator->with($data);

		if ($this->userValidator->passes())
		{
			if (User::create($data))
			{
				return Response::json(array(
					'status' => 'success',
					'message' => 'User registered. Please login to continue.'
				));
			} else {
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! Failed to register user.'
				));
			}
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => $this->userValidator->getErrors()
			));
		}
	}

	/**
	 * Change password
	 *
	 * @return Response
	 */
	public function putChangepassword()
	{
		$data = Input::get('user');

		$user = User::find(Auth::id());

		if (!$user)
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'User not found.'
			));
		}

		if (!Hash::check($data['old_password'], $user->password))
		{
			return Response::json(array(
				'status' => 'error',
				'message' => 'Old password does not match.'
			));
		}

		$user->password = Hash::make($data['new_password']);

		if ($user->save())
		{
			return Response::json(array(
				'status' => 'success',
				'message' => 'Password updated.'
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'Oops ! Failed to update password.'
			));
		}
	}

	/**
	 * Forgot password
	 *
	 * @return Response
	 */
	public function forgot($id)
	{
		$data = Input::get('user');

                $user = User::where('username', '=', $data['input'])->first();
                if (!$user)
		{
	                $user = User::where('email', '=', $data['input'])->first();
	                if (!$user)
			{
				return Response::json(array(
					'status' => 'error',
					'message' => 'Oops ! User not found.'
				));
	                }
                }

	}

	/**
	 * Logout user
	 *
	 * @return Response
	 */
	public function getProfile()
	{
		$data = User::where('id', '=', Auth::id())->get();

		if ($data)
		{
			return Response::json(array(
				'status' => 'success',
				'data' => array('user' => $data)
			));
		} else {
			return Response::json(array(
				'status' => 'error',
				'message' => 'User not found.'
			));
		}
	}
}
