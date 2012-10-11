<?php 

/**
 * Grunt
 *
 * @package  Grunt
 * @version  0.1.0
 * @author   Taufan Aditya
 */

namespace Grunt\Tests;

use Grunt\Session;

class SessionTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor()
	{
		$session = new Session('localhost');

		$this->assertInstanceOf('\\Grunt\\Session', $session);
	}

	public function testGetHost()
	{
		$session = new Session('localhost');

		$this->assertEquals('localhost', $session->getHost());
	}

	public function testGetConnection()
	{
		$session = new Session('localhost');

		$this->assertEquals('resource', gettype($session->getConnection()));
	}

	public function testValid()
	{
		$session = new Session('localhost');

		$this->assertTrue($session->valid());
	}
}