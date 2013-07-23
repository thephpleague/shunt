<?php 

/**
 * Grunt
 *
 * @package  Grunt
 * @version  1.0.0
 * @author   Taufan Aditya
 */

namespace Grunt\Tests;

use Grunt\Auth;
use Grunt\Session;

class AuthTest extends \PHPUnit_Framework_TestCase {

	public function testConstructor()
	{
		$credential = array('username' => 'grunt', 'password' => 'hearmyroar');
		$auth = new Auth(array('auth_password' => $credential));

		$this->assertInstanceOf('\\Grunt\\Auth', $auth);
	}

	public function testGetCredential()
	{
		$credential = array('username' => 'grunt', 'password' => 'hearmyroar');
		$auth = new Auth(array('auth_password' => $credential));

		$this->assertArrayHasKey('auth_password', $auth->getCredential());
	}

	public function testAuthorize()
	{
		$credential = array('username' => 'grunt', 'password' => 'hearmyroar');
		$session = new Session('localhost');
		$auth = new Auth(array('auth_password' => $credential));

		$this->assertTrue($auth->authorize($session, 'auth_password', $credential));
	}
}