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
	 * @return bool
	 */
	public function authorize(Session $session)
	{
		$connection = $session->getConnection();
		$credential = $this->getCredential();

		if (array_key_exists('auth_pubkey_file', $credential)) {
			$type = 'auth_pubkey_file'; 
			$data = $credential['auth_pubkey_file'];
		} elseif (array_key_exists('auth_password', $credential)) {
			$type = 'auth_password';
			$data = $credential['auth_password'];
		} else {
			$type = 'auth_none';
			$data = $credential['auth_none'];
		}

		array_unshift($data, $connection);

		// @codeCoverageIgnoreStart
		try {
			call_user_func_array(Auth::FUNCTION_PREFIX . $type, $data);
		} catch(\ErrorException $e) {
			$connection = NULL;
		}

		$session->setConnection($connection);

		return (bool) (is_resource($connection));
		// @codeCoverageIgnoreEnd
	}

}