<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Tests\Command;

use League\Shunt\Command\AboutCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AboutCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteAboutCommand()
    {
        $command = new AboutCommand();
        $command->setApplication(new Application());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => 'about'));

        $this->assertContains('Shunt is PHP library for executing commands in parallel on multiple remote machines, via SSH', $commandTester->getDisplay());
    }
}