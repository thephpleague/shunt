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

use League\Shunt\Command\ArbitraryCommand;
use League\Shunt\Tests\Mocks\MockApplication;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use ReflectionFunction;

class ArbitraryCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteAboutCommand()
    {
    	$name = 'something';
    	$description = 'Some shunt task';
    	$callable = function($g){
    		$g->run('okay');
    	};

        $command = new ArbitraryCommand($name, $description, new ReflectionFunction($callable));
        $command->setApplication(new MockApplication());

        $this->assertEquals($name, $command->getName());
        $this->assertEquals($description, $command->getDescription());

        $commandTester = new CommandTester($command);

        $this->assertEquals(0, $commandTester->execute(array('command' => 'something'), array('verbosity' => OutputInterface::VERBOSITY_DEBUG)));
    }
}