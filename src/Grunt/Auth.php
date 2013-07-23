<?php

/**
 * Grunt
 *
 * @package  Grunt
 * @version  1.0.0
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
	 * @var string SSH auth public key file type prefix
	 */
	const PUBKEY_FILE = 'auth_pubkey_file';

	/**
	 * @var string SSH auth password type prefix
	 */
	const PASSWORD = 'auth_password';

	/**
	 * @var string SSH auth none type prefix
	 */
	const NONE = 'auth_none';

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

		if (array_key_exists(Auth::PUBKEY_FILE, $credential)) {
			$type = Auth::PUBKEY_FILE; 
			$data = $credential[Auth::PUBKEY_FILE];
		} elseif (array_key_exists(Auth::PASSWORD, $credential)) {
			$type = Auth::PASSWORD;
			$data = $credential[Auth::PASSWORD];
		} else {
			$type = Auth::NONE;
			$data = $credential[Auth::NONE];
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