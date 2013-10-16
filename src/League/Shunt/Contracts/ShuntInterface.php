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

use League\Shunt\Contracts\SessionInterface;
use League\Shunt\Contracts\AuthInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Shunt Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface ShuntInterface
{
    const VERSION = '2.0.0';
    const COMMAND_SEPARATOR = ';';
    const HOLDER = 'Shuntfile';
    const KEY_HOSTS = 'hosts';
    const KEY_AUTH = 'auth';
    const KEY_TASKS = 'tasks';
    const KEY_AUTH_PASSWORD = 'password';
    const KEY_AUTH_PUBKEY = 'pubkeyfile';
    const KEY_AUTH_PRIVKEY = 'privkeyfile';
    const SIGNATURE_VAR = '<required> $s';
    const SIGNATURE_CLASS = '<required> Shunt $s';
    const SIGNATURE_NAMESPACE_CLASS = '<required> League\Shunt\Shunt $s';

    /**
     * Get current Shunt version
     *
     * @return string Shunt version
     */
    public static function version();

    /**
     * Get Shunt file holder
     *
     * @return string Shunt file holder
     */
    public static function holder();

    /**
     * Shunt instantiation
     *
     * @param  SessionInterface $session
     * @param  AuthInterface    $auth
     * @param  OutputInterface  $output
     * @return void
     * @throws RuntimeException
     */
    public function __construct(SessionInterface $session, AuthInterface $auth, OutputInterface $output);

    /**
     * Session getter
     *
     * @return SessionInterface
     */
    public function getSession();

    /**
     * Auth getter
     *
     * @return AuthInterface
     */
    public function getAuth();

    /**
     * SFTP Handler
     *
     * @return SFTPInterface
     */
    public function sftp();

    /**
     * SCP Handler
     *
     * @return SCPInterface
     */
    public function scp();

    /**
     * Shunt chainable process checker
     *
     * @return bool TRUE if `runOpen` already called, FALSE otherwise
     */
    public function inProcess();

    /**
     * Shunt opening chainable command
     *
     * @param  string         $command Command to execute
     * @return ShuntInterface
     */
    public function runOpen($command);

    /**
     * Shunt closing chainable command
     *
     * @param  string         $command Command to execute
     * @return ShuntInterface
     */
    public function runClose($command = null);

    /**
     * Shunt runner
     *
     * @param  string $command Command to execute
     * @param  bool   $retval  Return value
     * @return mixed  $retval or null
     */
    public function run($command, $retval = FALSE);

    /**
     * Register error handler
     */
    public static function registerErrorHandler();

    /**
     * Error handler
     *
     * @throws ErrorException
     */
    public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext);
}
