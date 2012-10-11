<?php

/**
 * Grunt
 *
 * @package  Grunt
 * @version  0.1.0
 * @author   Taufan Aditya
 */

namespace Grunt;

use Grunt\Session;

/**
 * Grunt Session class
 *
 * @package  Grunt
 * @category Core Class
 * @author   Taufan Aditya
 */
class Auth {

	/**
	 * @var string SSH auth function prefix
	 */
	const FUNCTION_PREFIX = 'ssh2_';

	/**
	 * @var array SSH auth credential
	 */
	protected $credential;

	/**
	 * Auth constructor
	 *
	 * @param  array credential
	 * @return void
	 */
	public function __construct($credential)
	{
		$this->credential = $credential;
	}

	/**
	 * Get the auth credential
	 *
	 * @return array 
	 */
	public function getCredential()
	{
		return $this->credential;
	}

	/**
	 * Authorize method
	 *
	 * @param  Session SSH session
	 * @param  string  Auth type
	 * @param  array   Auth data
	 * @return bool
	 */
	public function authorize(Session $session, $type, $data)
	{
		$connection = $session->getConnection();
		array_unshift($data, $connection);

		try {
			call_user_func_array(Auth::FUNCTION_PREFIX . $type, $data);
		} catch(\ErrorException $e) {
			$connection = NULL;
		}

		$session->setConnection($connection);

		return (bool) (is_resource($connection));
	}

}