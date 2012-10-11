<?php

/**
 * Grunt
 *
 * @package  Grunt
 * @version  0.1.0
 * @author   Taufan Aditya
 */

namespace Grunt;

use Grunt\Grunt;
use Grunt\Session;
use Grunt\Auth;

/**
 * Grunt Console class
 *
 * @package  Grunt
 * @category Core Class
 * @author   Taufan Aditya
 */
class Console {

	/**
	 * @var array Collected hosts information
	 */
	protected static $hosts = array('local' => 'localhost');

	/**
	 * @var array Collected authentification information
	 */
	protected static $auth = array('auth_none' => array('username' => 'root'));

	/**
	 * @var array Collected tasks information
	 */
	protected static $tasks = array();

	/**
	 * @var array Tasks abbreviation
	 */
	private static $abbreviation = array('h', 'v');

	/**
	 * General output CLI response
	 * 
	 * @param  string Output
	 * @return stream
	 */
	public static function out($output = '')
	{
		fwrite(STDOUT, $output."\n");
	}

	/**
	 * Print CLI response
	 * 
	 * @param  string data
	 * @return stream
	 */
	public static function printOut($data) 
	{
		fwrite(STDOUT, str_pad(' ', 2, '*').' '.$data.PHP_EOL);
	}

	/**
	 * Hosts setter
	 *
	 * @param  string Host nickname
	 * @param  string Host name
	 * @return void
	 */
	public static function setHost($nickname, $hostname)
	{
		static::$hosts[$nickname] = $hostname;
	}

	/**
	 * Hosts getter
	 *
	 * @return array Hosts collection
	 */
	public static function getHost()
	{
		return static::$hosts;
	}

	/**
	 * Auth setter
	 *
	 * @param  string Auth type
	 * @param  string Auth data
	 * @return void
	 */
	public static function setAuth($type, $data)
	{
		static::$auth[$type] = $data;
	}

	/**
	 * Auth getter
	 *
	 * @return array Auth collection
	 */
	public static function getAuth()
	{
		return static::$auth;
	}

	/**
	 * Tasks setter
	 *
	 * @param  string Task name
	 * @param  object Task Function
	 * @return void
	 */
	public static function setTask($name, $reflector)
	{
		$abbreviation = substr($name, 0, 1);

		if (in_array($abbreviation, static::$abbreviation)) {
			$abbreviation = substr($name, 0, 2);
		}

		array_push(static::$abbreviation, $abbreviation);

		$verbose = ucfirst(str_replace('_', ' ', $name));

		static::$tasks[$name] = compact('abbreviation', 'name', 'verbose', 'reflector');
	}

	/**
	 * Tasks getter
	 *
	 * @return array Tasks collection
	 */
	public static function getTask()
	{
		return static::$tasks;
	}

	/**
	 * Collect hosts and tasks information
	 * 
	 * @return void
	 */
	public static function collectData()
	{
		$gruntHolder = array();
		$currentDir = realpath($_SERVER['PWD']);

		if (($holder = $currentDir . DIRECTORY_SEPARATOR . Grunt::holder()) && @file_exists($holder)) {
			$gruntHolder = include $holder;
		}

		// Set the hosts 
		if (array_key_exists('hosts', $gruntHolder)) {
			foreach ($gruntHolder['hosts'] as $nickServer => $server) {
				self::setHost($nickServer, $server);
			}
		}

		// Set the auth credential 
		if (array_key_exists('auth', $gruntHolder)) {
			$credential = array_filter($gruntHolder['auth']);

			if (array_key_exists('password', $credential)) {
				self::setAuth('auth_password', $credential);
			} elseif (array_key_exists('pubkeyfile', $credential) && array_key_exists('privkeyfile', $credential)) {
				self::setAuth('auth_pubkey_file', $credential);
			}
		}

		// Set the available tasks
		if (array_key_exists('tasks', $gruntHolder)) {
			foreach ($gruntHolder['tasks'] as $taskName => $taskFunction) {
				// Validate each task
				if ($taskFunction instanceof \Closure) {
					$taskInformation = \ReflectionFunction::export($gruntHolder['tasks'][$taskName], TRUE);

					if (strpos($taskInformation, '<required> $g') !== FALSE) {
						self::setTask($taskName, new \ReflectionFunction($taskFunction));
					}
				}
			}
		}
	}

	/**
	 * Process arguments from $_SERVER
	 *
	 * @param  array Server arguments
	 * @return stream
	 */
	public static function processArguments(Array $arguments)
	{
		array_shift($arguments);

		if (empty($arguments)) {
			self::showInfo();
		} else {
			$commands = $arguments;
			$availableCommands = array('-h', '--help', '-v', '--version');

			// Get collected tasks
			$allTasks = self::getTask();

			foreach ($allTasks as $name => $task) {
				array_push($availableCommands, '-' . $task['abbreviation'], '--' . $task['name']);
			}

			while (current($commands) !== FALSE) {
				$command = current($commands);
				
				if ( ! in_array($command, $availableCommands)) {
					self::out(sprintf('Command \'%s\' not implemented', $command));
				} else {
					if ($command == '-h' || $command == '--help') {
						self::showInfo();
						end($commands);
					} elseif ($command == '-v' || $command == '--version') {
						self::showVersion();
						end($commands);
					} else {
						$requestedTaskName = 'undefined';

						if (preg_match('/^\-\-(.+)$/', $command, $matches) && count($matches) == 2) {
							$requestedTaskName = $matches[1];
						} elseif (preg_match('/^\-(.+)$/', $command, $matches) && count($matches) == 2) {

							foreach ($allTasks as $name => $task) {
								if ($matches[1] === $task['abbreviation']) {
									$requestedTaskName = $name;
								}
							}
						}

						if ($requestedTaskName !== 'undefined') {
							$hostArgs = next($commands);

							if ($hostArgs == FALSE) {
								$hosts = self::getHost();
							} else {
								$nicknames = explode(',', $hostArgs);

								$availableHosts = self::getHost();
								$hosts = array();

								foreach ($nicknames as $nick) {
									$hosts[$nick] = array_key_exists($nick, $availableHosts) ? $availableHosts[$nick] : $nick;
								}
							}

							foreach ($hosts as $nick => $server) {

								self::printOut('Execute ' . $requestedTaskName . ' on server ' . $nick);
								$requestedTask = $allTasks[$requestedTaskName];

								// Prepare grunt components
								$session = new Session($server);
								$auth = new Auth(self::getAuth());

								// Build the Grunt runner
								try {
									$grunt = new Grunt($session, $auth);

								} catch (\RuntimeException $e) {
									self::printOut($e->getMessage());
									self::out();
									continue;
								}

								self::printOut('Authorized');

								// Invoke the task
								$reflector = $requestedTask['reflector'];
								$reflector->invoke($grunt);
							}

						} else {
							self::out('Something goes wrong...');
						}

						end($commands);
					}
				}

				next($commands);
			}
		}
	}

	/**
	 * Show console usage information
	 *
	 * @return stream
	 */
	public static function showInfo() 
	{
		self::out('Usage : grunt <task>');
		self::out('Available Tasks :');
		self::out(str_pad(' -h|--help', 30, ' ') . 'Show this information');
		self::out(str_pad(' -v|--version', 30, ' ') . 'Show Grunt version');

		// Display collected tasks
		$tasks = self::getTask();

		if ( ! empty($tasks)) {
			foreach ($tasks as $task) {
				self::out(str_pad(' -' . $task['abbreviation'] . '|--' . $task['name'], 30, ' ') . $task['verbose']);
			}
		}
	}

	/**
	 * Show version information
	 *
	 * @return stream
	 */
	public static function showVersion() 
	{
		self::out('v.'.Grunt::version());
	}

	/**
	 * Main console runner
	 *
	 * @return stream
	 */
	public static function execute()
	{
		// Set error handler
		set_error_handler(array('\\Grunt\\Grunt', 'handleError'));

		self::out('Grunt v.' . Grunt::version() . ' by Taufan Aditya' . "\n");
		self::collectData();
		self::processArguments($_SERVER['argv']);
		self::out();
	}

}