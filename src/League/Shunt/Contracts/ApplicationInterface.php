<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Contracts;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Shunt Console Application Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface ApplicationInterface
{
    /**
     * Shunt getter
     *
     * @return Shunt
     */
    public function getShunt();

    /**
     * Collect hosts and tasks information
     *
     * @return void
     */
    public function collectData();

    /**
     * Hosts setter
     *
     * @param  string Host nickname
     * @param  string Host name
     * @return void
     */
    public function setHost($nickname, $hostname);

    /**
     * Host getter
     *
     * @param  string Host nickname
     * @return string Host information
     */
    public function getHost($nickname);

    /**
     * Hosts getter
     *
     * @return array Hosts collection
     */
    public function getHosts();

    /**
     * Auth setter
     *
     * @param  string Auth type
     * @param  string Auth data
     * @return void
     */
    public function setAuth($type, $data);

    /**
     * Auth getter
     *
     * @return array Auth collection
     */
    public function getAuth();

    /**
     * Tasks setter
     *
     * @param  string Task name
     * @param  object Task Function
     * @return void
     */
    public function setTask($name, $reflector);

    /**
     * Tasks getter
     *
     * @return array Tasks collection
     */
    public function getTasks();

    /**
     * Get host name from input arguments
     *
     * @param InputInterface
     * @return array
     */
    public function getHostNames(InputInterface $input);
}
