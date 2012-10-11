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
use Grunt\Grunt;

class GruntTest extends \PHPUnit_Framework_TestCase {

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
		$this->assertInstanceOf('\\Grunt\\Grunt', $this->grunt);
	}

	public function testVersion()
	{
		$this->assertEquals(Grunt::VERSION, call_user_func(array($this->grunt, 'version')));
	}

	public function testHolder()
	{
		$this->assertEquals(Grunt::HOLDER, call_user_func(array($this->grunt, 'holder')));
	}

	public function testRegisterAutoloader()
	{
		Grunt::registerAutoloader();

		$this->assertTrue(in_array(array('Grunt\\Grunt', 'autoload'), spl_autoload_functions()));
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