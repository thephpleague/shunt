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
use League\Shunt\Contracts\AuthInterface;
use League\Shunt\Contracts\ShuntInterface;
use League\Shunt\BaseObject;
use League\Shunt\SCP;
use League\Shunt\SFTP;
use Symfony\Component\Console\Output\OutputInterface;
use ErrorException, RuntimeException;

/**
 * Shunt Core class
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class Shunt extends BaseObject implements ShuntInterface
{
    /**
     * @var object Shunt session
     */
    protected $session;

    /**
     * @var object Shunt auth
     */
    protected $auth;

    /**
     * @var bool
     */
    protected $inProcess = false;

    /**
     * @var array
     */
    protected $chainableCommand = array();

    /**
     * Get current Shunt version
     *
     * @return string Shunt version
     */
    public static function version()
    {
        return self::VERSION;
    }

    /**
     * Get Shunt file holder
     *
     * @return string Shunt file holder
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
        self::registerErrorHandler();

        // Set the base object properties
        parent::__construct($session, $output);

        $this->printOut('<info>Connecting...</info>');
        $this->printDebug('$session => '.var_export($session,true));
        $this->printVerbose('Connecting to '.$session->getHost().' at port '.$session->getPort());

        // Validate ssh2 session
        if ( ! $session->valid()) {
            throw new RuntimeException('SSH connection failed.');
        }

        $this->printVerbose('Connected.');

        $this->printDebug('$auth => '.var_export($auth,true));
        $this->printVerbose('Authorize...');

        // Authorize
        try {
            $authorized = $auth->authorize($session);
        } catch (ErrorException $e) {
            throw new RuntimeException('SSH authorization failed. REASON : '.$e->getMessage());
        }

        $this->printVerbose('Authorized');

        $this->session = $session;
        $this->auth = $auth;

        $this->printDebug('HOST FINGERPRINT: '.ssh2_fingerprint($this->session->getConnection()));
    }

    /**
     * @{inheritDoc}
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @{inheritDoc}
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @{inheritDoc}
     */
    public function sftp()
    {
        return new SFTP($this->session, $this->output);
    }

     /**
     * @{inheritDoc}
     */
    public function scp()
    {
        return new SCP($this->session, $this->output);
    }

    /**
     * @{inheritDoc}
     */
    public function inProcess()
    {
        return $this->inProcess;
    }

    /**
     * @{inheritDoc}
     */
    public function runOpen($command)
    {
        $this->inProcess = true;

        return $this->run($command);
    }

    /**
     * @{inheritDoc}
     */
    public function runClose($command = '')
    {
        $this->inProcess = false;

        return $this->run($command);
    }

    /**
     * @{inheritDoc}
     */
    public function run($command, $retval = FALSE)
    {
        $this->addCommand($command);

        if ($this->inProcess) {
            // Chainable command triggered
            return $this;
        }

        // Get the full-commands
        $command = $this->getCommands();

        $host = $this->session->getHost();
        $connection = $this->session->getConnection();
        $halt = FALSE;

        $retval or $this->printOut('<comment>' . $host . '</comment> < <info>' . $command.'</info>');

        // Execute the command
        $stream = ssh2_exec($connection, $command);

        $this->printVerbose('Fetching Streams...');

        $errStream = ssh2_fetch_stream($stream, 1);
        $dioStream = ssh2_fetch_stream($stream, 0);

        stream_set_blocking($errStream, TRUE);
        stream_set_blocking($dioStream, TRUE);

        $this->printVerbose('Get Streams content...');

        $resultErr = stream_get_contents($errStream);
        $resultDio = stream_get_contents($dioStream);
        $resultDio = empty($resultDio) ? '[OK]' : $resultDio;

        if (!empty($resultErr)) $halt = TRUE;

        $this->printVerbose('Closing stream...');
        fclose($stream);

        if ($halt) {
            $this->printOut($resultErr);
            $this->printOut('<error>Aborted</error>');
        } else {
            if ($resultDio !== '[OK]') {
                $resultDioArray = array_filter(explode("\n", $resultDio));

                foreach ($resultDioArray as $i => $resultDioLine) {
                    $this->printOut((($i === 0) ? '<comment>'.$host.'</comment>' : str_pad(' ', strlen($host))).' > <info>'.$resultDioLine.'</info>');
                }
            } else {
                $this->printOut('<comment>'.$host.'</comment> > <info>'.$resultDio.'</info>');
            }
        }

        if ($retval) return (int) $halt;
    }

    /**
     * {@inheritDoc}
     */
    public static function registerErrorHandler()
    {
        // Set error handler
        set_error_handler('\League\Shunt\Shunt::handleError');
    }

    /**
     * {@inheritDoc}
     */
    public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // Error was suppressed with the '@' operator
        if (0 === error_reporting()) return FALSE;

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Stack chainable commands
     *
     * @param string $command Command to execute
     */
    protected function addCommand($command)
    {
        $this->chainableCommand[] = $command;
    }

    /**
     * Get stacked chainable commands
     *
     * @return string Command to execute
     */
    protected function getCommands()
    {
        return implode(self::COMMAND_SEPARATOR, $this->chainableCommand);
    }
}
