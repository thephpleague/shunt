<?php 

/**
 * Grunt
 *
 * @package  Grunt
 * @version  0.1.0
 * @author   Taufan Aditya
 */

namespace Grunt\Tests;

use Grunt\Auth;
use Grunt\Session;

class AuthTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor()
	{
		$auth = new Auth(array('username' => 'root'));

		$this->assertInstanceOf('\\Grunt\\Auth', $auth);
	}

	public function testGetCredential()
	{
		$auth = new Auth(array('username' => 'root'));

		$this->assertArrayHasKey('username', $auth->getCredential());
	}

	public function testAuthorize()
	{
		$credential = array('username' => 'root');
		$session = new Session('localhost');
		$auth = new Auth(array('auth_none' => $credential));

		$this->assertTrue($auth->authorize($session, 'auth_none', $credential));
	}
}