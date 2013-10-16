<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Tests;

use League\Shunt\Auth;
use League\Shunt\Session;
use PHPUnit_Framework_TestCase;

/**
 * Shunt Auth Unit-Test
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class AuthTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $auth = new Auth(array('auth_password' => $credential));

        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\AuthInterface', $auth);
    }

    public function testGetCredentialAuthNone()
    {
        $credential = array('username' => 'shunt');
        $auth = new Auth(array('auth_none' => $credential));

        $this->assertArrayHasKey('auth_none', $auth->getCredential());
    }

    public function testGetCredentialAuthPublicKey()
    {
        $credential = array(
            'pubkeyfile' => NULL,
            'privkeyfile' => NULL,
            'passphrase' => NULL);

        $auth = new Auth(array('auth_pubkey_file' => $credential));

        $this->assertArrayHasKey('auth_pubkey_file', $auth->getCredential());
    }

    public function testGetCredentialAuthPassword()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $auth = new Auth(array('auth_password' => $credential));

        $this->assertArrayHasKey('auth_password', $auth->getCredential());
    }

    public function testParseCredential()
    {
        // Auth pubkey test
        $credential = array('pubkeyfile' => '/path/to/pubkey', 'privkeyfile' => '/path/to/privkey', 'passphrase' => 'secret');
        $auth = new Auth(array('auth_pubkey_file' => $credential));
        $parsedCredential = $auth->parse($auth->getCredential());

        $this->assertCount(2, $parsedCredential);
        $this->assertEquals('auth_pubkey_file', $parsedCredential[0]);
    }

    public function testValidAuthorize()
    {
        // Auth none test
        $session = new Session('localhost');

        $auth = new Auth(array());
        $this->assertTrue($auth->authorize($session));

        // Auth password test
        $session = new Session('localhost');

        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $auth = new Auth(array('auth_password' => $credential));

        $this->assertTrue($auth->authorize($session));
    }
}
