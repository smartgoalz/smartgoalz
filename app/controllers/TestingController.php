<?php

use Smartgoalz\Gateways\JournalGateway;

class TestingController extends BaseController {

	public static $test = array(
		//'id' => Auth::id(),
	);

	public function __construct(JournalGateway $journalGateway)
	{
		$this->journalGateway = $journalGateway;
		static::$test['a'] = Auth::id();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		dd (static::$test);
		$data = DB::getQueryLog();
		return View::make('testing')->with('data', $data);
	}

}
