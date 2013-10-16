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

use League\Shunt\Contracts\PrintableInterface;
use League\Shunt\Contracts\SessionInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base object
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
abstract class BaseObject implements PrintableInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @param SessionInterface
     * @param OutputInterface
     */
    public function __construct(SessionInterface $session, OutputInterface $output)
    {
        $this->session = $session;
        $this->output = $output;
    }

    /**
     * Print normal information
     *
     * @param string
     */
    public function printOut($message)
    {
        $this->output->writeln($message);
    }

    /**
     * Print verbose information
     *
     * @param string
     */
    public function printVerbose($message)
    {
        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $this->printOut($message);
        }
    }

    /**
     * Print debug information
     *
     * @param string
     */
    public function printDebug($message)
    {
        if ($this->output->getVerbosity() == OutputInterface::VERBOSITY_DEBUG) {
            $this->printOut('[DEBUG] '.$message);
        }
    }

    /**
     * Main Runner
     *
     * @param dynamic
     */
    protected function doRun()
    {
        $result = 0;
        $args = func_get_args();
        $host = $this->session->getHost();

        if (count($args) === 3) {
            list($method, $params, $retval) = $args;

            // Strip namespace 
            $method = trim(str_replace(__NAMESPACE__, '', $method),'\\');
            $command = $method.' => ['.implode(' , ', $params).']';
            $this->printOut('<comment>' . $host . '</comment> < <info>' . $command.'</info>');

            // Initial result evaluation
            $result = $retval;
            $halt = $result == false;

            // Prints process information
            // @codeCoverageIgnoreStart
            if ($halt) {
                $this->printOut('<error>Aborted</error>');
            } else {
                if (is_string($retval) || is_array($retval)) {

                    if(is_array($retval)) {
                        foreach ($retval as $key => $val) {
                            if ( ! is_numeric($key)) $resultDioArray[] = $key.':'.$val;
                        }
                    } else {
                        $resultDioArray = array_filter(explode("\n", $retval));
                    }


                    foreach ($resultDioArray as $i => $resultDioLine) {
                        $this->printOut((($i === 0) ? '<comment>'.$host.'</comment>' : str_pad(' ', strlen($host))).' > <info>'.$resultDioLine.'</info>');
                    }
                } else {
                    $this->printOut('<comment>'.$host.'</comment> > <info>[OK]</info>');
                }
            }
            // @codeCoverageIgnoreEnd
        }

        return $result;
    }
}
