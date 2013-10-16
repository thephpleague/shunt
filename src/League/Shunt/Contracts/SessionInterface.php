<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Contracts;

/**
 * Shunt Session Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface SessionInterface
{
    /**
     * Get current connection
     *
     * @return resource SSH connection
     */
    public function getConnection();

    /**
     * Set current connection
     *
     * @param resource SSH connection
     * @return void
     */
    public function setConnection($connection);

    /**
     * Get current host
     *
     * @return string Hostname
     */
    public function getHost();

    /**
     * Get current port
     *
     * @return int Port
     */
    public function getPort();

    /**
     * Set current host
     *
     * @param string Hostname
     * @return void
     */
    public function setHost($host);

    /**
     * Validate connection
     *
     * @return bool Whether SSH connection is a valid resource or not
     */
    public function valid();

}
