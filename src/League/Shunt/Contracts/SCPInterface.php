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
 * Shunt SCP Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface SCPInterface
{
    /**
     * Send a file from local to remote path
     *
     * @param  string $localFile
     * @param  string $remoteFile
     * @return bool
     */
    public function put($localFile = '', $remoteFile = '');

   /**
     * Get a file from remote to local path
     *
     * @param  string $remoteFile
     * @param  string $localFile
     * @return bool
     */
    public function get($remoteFile = '', $localFile = '');
}
