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

use League\Shunt\Session;
use PHPUnit_Framework_TestCase;

/**
 * Shunt Session Unit-Test
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $session = new Session('localhost');

        $this->assertInstanceOf('\\League\\Shunt\\Contracts\\SessionInterface', $session);
    }

    public function testSetHost()
    {
        $session = new Session('localhost');

        $session->setHost('localhost:8080');

        $this->assertEquals('localhost', $session->getHost());
        $this->assertEquals(8080, $session->getPort());
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
