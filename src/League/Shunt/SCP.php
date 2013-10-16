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
use RuntimeException;

/**
 * Shunt SCP class
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class SCP implements SCPInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @param SessionInterface
     * @throws RuntimeException
     */
    public function __construct(SessionInterface $session)
    {
        if ( ! $session->valid()) throw new RuntimeException('SSH connection failed.');

        $this->session = $session;
    }

    /**
     * @{inheritDoc}
     */
    public function put($localFile = '', $remoteFile = '')
    {
        return ssh2_scp_send($this->session->getConnection(), $localFile, $remoteFile);
    }

   /**
     * @{inheritDoc}
     */
    public function get($remoteFile = '', $localFile = '')
    {
        return ssh2_scp_recv($this->session->getConnection(), $remoteFile, $localFile);
    }
}
