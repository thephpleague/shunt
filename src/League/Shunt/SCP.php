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

use League\Shunt\Contracts\SCPInterface;
use League\Shunt\Contracts\SessionInterface;
use League\Shunt\BaseObject;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

/**
 * Shunt SCP class
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class SCP extends BaseObject implements SCPInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @param SessionInterface
     * @param OutputInterface
     * @throws RuntimeException
     */
    public function __construct(SessionInterface $session, OutputInterface $output)
    {
        // Set the base object properties
        parent::__construct($session, $output);

        if ( ! $session->valid()) throw new RuntimeException('SSH connection failed.');

        $this->session = $session;
    }

    /**
     * @{inheritDoc}
     */
    public function put($localFile = '', $remoteFile = '')
    {
        return $this->doRun(__METHOD__, func_get_args(), ssh2_scp_send($this->session->getConnection(), $localFile, $remoteFile));
    }

   /**
     * @{inheritDoc}
     */
    public function get($remoteFile = '', $localFile = '')
    {
        return $this->doRun(__METHOD__, func_get_args(), ssh2_scp_recv($this->session->getConnection(), $remoteFile, $localFile));
    }
}
