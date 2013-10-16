<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt;

use League\Shunt\Contracts\SessionInterface;
use Exception;

/**
 * Shunt Session class
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class Session implements SessionInterface
{
    /**
     * @var string Host (Hostname plus Port)
     */
    protected $host;

    /**
     * @var string Hostname
     */
    protected $hostname;

    /**
     * @var string Port
     */
    protected $port = 22;

    /**
     * @var resource SSH connection
     */
    protected $connection;

    /**
     * Session constructor
     *
     * @param  string $host     Hostname
     * @param  array  $method
     * @param  array  $callback
     * @return void
     */
    public function __construct($host, $method = array(), $callback = array())
    {
        $this->setHost($host);

        try {
            $connection = ssh2_connect($this->getHost(), $this->getPort(), $method, $callback);
        } catch (Exception $e) {
            $connection = null;
        }

        $this->setConnection($connection);
    }

    /**
     * @{inheritDoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @{inheritDoc}
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @{inheritDoc}
     */
    public function getHost()
    {
        return $this->hostname;
    }

    /**
     * @{inheritDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @{inheritDoc}
     */
    public function setHost($host)
    {
        $this->host = $host;

        if (strpos($host,':') !== false) {
            list($hostname, $port) = explode(':', $host);
            $this->hostname = $hostname;
            $this->port = (int) $port;
        } else {
            $this->hostname = $host;
        }
    }

    /**
     * @{inheritDoc}
     */
    public function valid()
    {
        return (bool) (is_resource($this->connection));
    }

}
