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
use League\Shunt\SCP;
use Symfony\Component\Console\Output\NullOutput;
use PHPUnit_Framework_TestCase;

/**
 * Shunt SCP Unit-Test
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class SCPTest extends PHPUnit_Framework_TestCase
{
    protected $shunt;
    protected $scp;

    public function setUp()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $session = new Session('localhost');
        $auth = new Auth(array('auth_password' => $credential));
        $output = new NullOutput();

        $this->shunt = new Shunt($session, $auth, $output);
        $this->scp = $this->shunt->scp();
    }

    public function testScpPutAndGet()
    {
        // Test upload
        $upload = $this->scp->put(__FILE__, 'test');

        $this->assertTrue($upload);

        // Test download
        $download = $this->scp->get('test', __FILE__.'.bak');

        $this->assertTrue($download);

        // Delete the downloaded file
        @unlink(__FILE__.'.bak');
    }
}
