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

/**
 * Shunt Auth Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface AuthInterface
{
    const FUNCTION_PREFIX = 'ssh2_';
    const PUBKEY_FILE = 'auth_pubkey_file';
    const PASSWORD = 'auth_password';
    const AGENT = 'auth_agent';
    const NONE = 'auth_none';

    /**
     * Get the auth credential
     *
     * @return array
     */
    public function getCredential();

    /**
     * Authorize method
     *
     * @param  Session SSH session
     * @return bool
     */
    public function authorize(SessionInterface $session);

}
