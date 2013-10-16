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
use League\Shunt\Shunt;
use League\Shunt\Tests\Mocks\MockOutput;
use PHPUnit_Framework_TestCase;

/**
 * Shunt Core Unit-Test
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class ShuntTest extends PHPUnit_Framework_TestCase
{
    protected $Shunt;

    public function setUp()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $session = new Session('localhost');
        $auth = new Auth(array('auth_password' => $credential));
        $output = new MockOutput();

        $this->shunt = new Shunt($session, $auth, $output);
    }

    public function testInvalidSessionInConstructor()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $session = new Session('nowhere', array(
                    'kex' => 'diffie-hellman-group1-sha1',
                    'client_to_server' => array(
                    'crypt' => '3des-cbc',
                    'comp' => 'none'),
                    'server_to_client' => array(
                    'crypt' => 'aes256-cbc,aes192-cbc,aes128-cbc',
                    'comp' => 'none')),
                    array('disconnect' => 'my_ssh_disconnect'));

        $auth = new Auth(array('auth_password' => $credential));
        $output = new MockOutput();

        $this->setExpectedException('RuntimeException', 'SSH connection failed.');

        $shunt = new Shunt($session, $auth, $output);
    }

    public function testInvalidAuthInConstructor()
    {
        $credential = array('username' => 'foo', 'password' => 'bar');
        $session = new Session('localhost');
        $auth = new Auth(array('auth_password' => $credential));
        $output = new MockOutput();

        $this->setExpectedException('RuntimeException','SSH authorization failed. REASON : ssh2_auth_password(): Authentication failed for foo using password');

        $shunt = new Shunt($session, $auth, $output);
    }

    public function testValidConstructor()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $session = new Session('localhost');
        $auth = new Auth(array('auth_password' => $credential));
        $output = new MockOutput();

        $shunt = new Shunt($session, $auth, $output);

        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\ShuntInterface', $shunt);
    }

    public function testVersion()
    {
        $this->assertEquals(Shunt::VERSION, call_user_func(array($this->shunt, 'version')));
    }

    public function testHolder()
    {
        $this->assertEquals(Shunt::HOLDER, call_user_func(array($this->shunt, 'holder')));
    }

    public function testHandleError()
    {
        $this->setExpectedException('\\ErrorException');

        Shunt::handleError(999, 'Something goes wrong', __FILE__, 56, array());
    }

    public function testGetters()
    {
        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\SessionInterface', $this->shunt->getSession());
        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\AuthInterface', $this->shunt->getAuth());
        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\SCPInterface', $this->shunt->scp());
        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\SFTPInterface', $this->shunt->sftp());
    }

    public function testRun()
    {
        // Mark open state
        $this->shunt->runOpen('whoami');
        $this->assertTrue($this->shunt->inProcess());

        $this->shunt->run('ls');
        $this->assertTrue($this->shunt->inProcess());

        $retval = $this->shunt->runClose('php -i', TRUE);

        $this->assertFalse($this->shunt->inProcess());

        $this->assertEquals(0, $retval);

        // Test invalid command
        $retval = $this->shunt->run('whoareyou', TRUE);
        $this->assertEquals(1, $retval);
    }
}
