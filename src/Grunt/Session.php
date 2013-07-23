<?php

/**
 * Grunt
 *
 * @package  Grunt
 * @version  1.0.0
 * @author   Taufan Aditya
 */

namespace Grunt;

/**
 * Grunt Session class
 *
 * @package  Grunt
 * @category Core Class
 * @author   Taufan Aditya
 */
class Session {

	/**
	 * @var string Hostname
	 */
	protected $host;

	/**
	 * @var resource SSH connection
	 */
	protected $connection;

	/**
	 * Session constructor
	 *
	 * @param  string Hostname
	 * @return void
	 */
	public function __construct($host)
	{
		$this->setHost($host);

		// @codeCoverageIgnoreStart
		try {
			$connection = ssh2_connect($this->getHost());
		} catch (\ErrorException $e) {
			return NULL;
		}
		// @codeCoverageIgnoreEnd

		$this->setConnection($connection);
	}

	/**
	 * Get current connection
	 *
	 * @return resource SSH connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Set current connection
	 *
	 * @param resource SSH connection
	 * @return void
	 */
	public function setConnection($connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Get current host
	 *
	 * @return string Hostname
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set current host
	 *
	 * @param string Hostname
	 * @return void
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}

	/**
	 * Validate connection
	 *
	 * @return bool Whether SSH connection is a valid resource or not
	 */
	public function valid()
	{
		return (bool) (is_resource($this->connection));
	}

}