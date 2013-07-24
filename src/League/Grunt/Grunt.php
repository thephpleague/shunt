<?php

/**
 * Grunt
 *
 * @package  Grunt
 * @version  1.0.1
 * @author   Taufan Aditya
 */

namespace League\Grunt;

use League\Grunt\Session;
use League\Grunt\Auth;
use League\Grunt\Console;

/**
 * Grunt Main class
 *
 * @package  Grunt
 * @category Core Class
 * @author   Taufan Aditya
 */
class Grunt
{
    /**
     * @var string Grunt version
     */
    const VERSION = '1.0.1';

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
            $authorized = $auth->authorize($session);

            if (! $authorized) {
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

        $retval or Console::printOut('[' . $host . ']: ' . $command);

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
