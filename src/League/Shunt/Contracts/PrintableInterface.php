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

/**
 * Printable Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface PrintableInterface
{
    /**
     * Print normal information
     *
     * @param string
     */
    public function printOut($message);

    /**
     * Print verbose information
     *
     * @param string
     */
    public function printVerbose($message);

    /**
     * Print debug information
     *
     * @param string
     */
    public function printDebug($message);
}
