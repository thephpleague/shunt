<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Tests\Mocks;

use League\Shunt\Contracts\ApplicationInterface;
use League\Shunt\Session;
use League\Shunt\Auth;
use League\Shunt\Tests\Mocks\MockOutput;
use League\Shunt\Tests\Mocks\MockShunt;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class MockApplication extends Application implements ApplicationInterface
{
    /**
     * Mock shunt instance
     * @{inheritDoc}
     */
    public function getShunt()
    {
        $session = new Session('nowhere');
        $auth = new Auth(array());
        $output = new MockOutput();

        return new MockShunt($session, $auth, $output);
    }

    /**
     * @{inheritDoc}
     */
    public function collectData()
    {
        // Do nothing
    }

   /**
     * @{inheritDoc}
     */
    public function setHost($nickname, $hostname)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getHost($nickname)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getHosts()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function setAuth($type, $data)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getAuth()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function setTask($name, $reflector)
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getTasks()
    {
        // Do nothing
    }

    /**
     * @{inheritDoc}
     */
    public function getHostNames(InputInterface $input)
    {
        // Do nothing
    }
}
