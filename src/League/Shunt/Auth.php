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

/**
 * Shunt Auth class
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class Auth implements AuthInterface
{
    /**
     * @var array SSH auth credential
     */
    protected $credential;

    /**
     * Auth constructor
     *
     * @param  array $credential
     * @return void
     */
    public function __construct($credential = array())
    {
        if (empty($credential)) $credential = array('auth_none' => array('undefined'));
        $this->credential = $credential;
    }

    /**
     * @{inheritDoc}
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @{inheritDoc}
     */
    public function authorize(SessionInterface $session)
    {
        $connection = $session->getConnection();
        $credential = $this->getCredential();

        list($type, $data) = $this->parse($credential);

        array_unshift($data, $connection);

        call_user_func_array(self::FUNCTION_PREFIX . $type, array_filter($data));

        $session->setConnection($connection);

        return $session->valid();
    }

    /**
     * Helper to parse the session
     *
     * @param array
     * @return array
     */
    public function parse($credential = array())
    {
        if (array_key_exists(self::PUBKEY_FILE, $credential)) {
            $type = self::PUBKEY_FILE;
            $data = $credential[self::PUBKEY_FILE];
        } elseif (array_key_exists(self::PASSWORD, $credential)) {
            $type = self::PASSWORD;
            $data = $credential[self::PASSWORD];
        } else {
            $type = self::NONE;
            $data = isset($credential[self::NONE]) ? $credential[self::NONE] : array();
        }

        return array($type, $data);
    }

}
