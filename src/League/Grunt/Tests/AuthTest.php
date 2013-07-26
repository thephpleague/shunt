<?php

/**
 * Grunt
 *
 * @package  Grunt
 * @version  1.0.1
 * @author   Taufan Aditya
 */

namespace League\Grunt\Tests;

use League\Grunt\Auth;
use League\Grunt\Session;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $credential = array('username' => 'grunt', 'password' => 'hearmyroar');
        $auth = new Auth(array('auth_password' => $credential));

        $this->assertInstanceOf('\\League\\Grunt\\Auth', $auth);
    }

    public function testGetCredentialAuthNone()
    {
        $credential = array('username' => 'grunt');
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
        $credential = array('username' => 'grunt', 'password' => 'hearmyroar');
        $auth = new Auth(array('auth_password' => $credential));

        $this->assertArrayHasKey('auth_password', $auth->getCredential());
    }

    public function testValidAuthorize()
    {
        $session = new Session('localhost');
        
        $credential = array('username' => 'grunt', 'password' => 'hearmyroar');
        $auth = new Auth(array('auth_password' => $credential));

        $this->assertTrue($auth->authorize($session));
    }
}
