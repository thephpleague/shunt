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
use League\Grunt\Grunt;

class GruntTest extends \PHPUnit_Framework_TestCase
{
    protected $grunt;

    public function setUp()
    {
        $credential = array('username' => 'grunt', 'password' => 'hearmyroar');
        $session = new Session('localhost');
        $auth = new Auth(array('auth_password' => $credential));

        $this->grunt = new Grunt($session, $auth);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('\\League\\Grunt\\Grunt', $this->grunt);
    }

    public function testVersion()
    {
        $this->assertEquals(Grunt::VERSION, call_user_func(array($this->grunt, 'version')));
    }

    public function testHolder()
    {
        $this->assertEquals(Grunt::HOLDER, call_user_func(array($this->grunt, 'holder')));
    }

    public function testHandleError()
    {
        $this->setExpectedException('\\ErrorException');

        Grunt::handleError(999, 'Something goes wrong', __FILE__, 56, array());
    }

    public function testRun()
    {
        $retval = $this->grunt->run('php -i', TRUE);

        $this->assertEquals(1, $retval);
    }
}
