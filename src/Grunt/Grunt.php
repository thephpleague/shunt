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
use Grunt\Auth;
use Grunt\Console;

/**
 * Grunt Main class
 *
 * @package  Grunt
 * @category Core Class
 * @author   Taufan Aditya
 */
class Grunt {

	/**
	 * @var string Grunt version
	 */
	const VERSION = '0.1.0';

	/**
	 * @var string Grunt file holder
	 */
	const HOLDER = 'Gruntfile';

	/**
	 * @var object Grunt session
	 */
	protected $session;

	/**
	 * @var object Grunt auth
	 */
	protected $auth;

	/**
	 * Universal Grunt Autoloader
	 *
	 * @param  string  class name called by SPL
	 * @return void
	 */
	public static function autoload($className)
	{
		// Ignore browser built-in request
		if (strpos($className, 'favicon.ico') !== FALSE) {
			return;
		}
		
		// PSR-0 Autoloader
		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';

		if ($lastNsPos = strripos($className, '\\')) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		$candidate = (isset($namespace)) ? current(explode('\\', $namespace)) : current(explode('_', $className));

		if ($candidate == __NAMESPACE__) {
			$fileName = str_replace($candidate . DIRECTORY_SEPARATOR, __DIR__ . DIRECTORY_SEPARATOR, $fileName);
		}

		require_once $fileName;
	}

	/**
	 * Autoloader register process
	 *
	 * @return void
	 */
	public static function registerAutoloader()
	{
		spl_autoload_register('\\Grunt\\Grunt::autoload');
	}

	/**
	 * Get current Grunt version
	 *
	 * @return string Grunt version
	 */
	public static function version()
	{
		return self::VERSION;
	}

	/**
	 * Get Grunt file holder
	 *
	 * @return string Grunt file holder
	 */
	public static function holder()
	{
		return self::HOLDER;
	}

	/**
	 * Grunt instantiation
	 *
	 * @param  Session 
	 * @param  Auth 
	 * @return void
	 */
	public function __construct(Session $session, Auth $auth)
	{
		if ($session->valid()) {
			$credential = $auth->getCredential();

			if (array_key_exists('auth_pubkey_file', $credential)) {
				$authorized = $auth->authorize($session, 'auth_pubkey_file', $credential['auth_pubkey_file']);
			} elseif (array_key_exists('auth_password', $credential)) {
				$authorized = $auth->authorize($session, 'auth_password', $credential['auth_password']);
			} else {
				$authorized = $auth->authorize($session, 'auth_none', $credential['auth_none']);
			}

			if ( ! $authorized) {
				throw new \RuntimeException('SSH authorization failed.');
			}

			$this->session = $session;
			$this->auth = $auth;
		} else {
			throw new \RuntimeException('SSH connection failed.');
		}
	}

	/**
	 * Grunt runner
	 *
	 * @param  string Command to execute 
	 * @param  bool   Return value
	 * @return mixed
	 */
	public function run($command, $retval = FALSE)
	{
		$host = $this->session->getHost();
		$connection = $this->session->getConnection();
		$halt = FALSE;

		Console::printOut('[' . $host . ']: ' . $command);

		$stream = ssh2_exec($connection, $command);

		$err_stream = ssh2_fetch_stream($stream, 1);
		$dio_stream = ssh2_fetch_stream($stream, 0);

		stream_set_blocking($err_stream, TRUE);
		stream_set_blocking($dio_stream, TRUE);

		$result_err = stream_get_contents($err_stream);
		$result_dio = stream_get_contents($dio_stream);
		$result_dio = empty($result_dio) ? '[OK]' : $result_dio;

		if (!empty($result_err)) {
			$halt = TRUE;
		}

		fclose($stream);

		if ($retval) {
			return (int) $halt;
		}
		
		if ($halt) {
			Console::printOut($result_err);
			Console::printOut('Aborting...');
		} else {
			if ($result_dio !== '[OK]') {
				$result_dio_array = array_filter(explode("\n", $result_dio));

				foreach ($result_dio_array as $result_dio_line) {
					Console::printOut('[out :: '.$host.']: '.$result_dio_line);
				}
			} else {
				Console::printOut('[out :: '.$host.']: '.$result_dio);
			}
		}
	}

	/**
	 * Error handler
	 */
	public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
	{
		// Error was suppressed with the '@' operator
		if (0 === error_reporting()) {
			return FALSE;
		}

		throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
}