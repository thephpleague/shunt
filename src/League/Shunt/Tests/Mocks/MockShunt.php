<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Tests\Mocks;

use League\Shunt\Contracts\ShuntInterface;
use League\Shunt\Contracts\SessionInterface;
use League\Shunt\Contracts\AuthInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MockShunt implements ShuntInterface
{
    /**
     * @{inheritDoc}
     */
    public static function version()
    {
        return self::VERSION;
    }

    /**
     * @{inheritDoc}
     */
    public static function holder()
    {
        return self::HOLDER;
    }

    /**
     * @{inheritDoc}
     */
    public function __construct(SessionInterface $session, AuthInterface $auth, OutputInterface $output)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getSession()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getAuth()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function sftp()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function scp()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function inProcess()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function runOpen($command)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function runClose($command = null)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function run($command, $retval = FALSE, callable $resultHandler = null)
    {
        return $command;
    }

    /**
     * @{inheritDoc}
     */
    public static function registerErrorHandler()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // Do nothing
    }
}
