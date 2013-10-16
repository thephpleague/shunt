<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Tests\Console;

use League\Shunt\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use Exception;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testApplicationLifeCycle()
    {
        // run the command application
        $recipe = include realpath(__DIR__.'/../Stubs/Shuntfile');
		$shuntApp = new Application($recipe, true);
		$applicationTester = new ApplicationTester($shuntApp);

		// Check the collector - recipe life-cycle
		$applicationTester->run(array('command' => 'list'));

		// Ensure it contains all specified hosts within recipe stub
		$this->assertContains('mirror', $applicationTester->getDisplay());

		// Ensure it contains all specified tasks within recipe stub
		$this->assertContains('read_home_dir', $applicationTester->getDisplay());
		$this->assertContains('print_php_info', $applicationTester->getDisplay());
		$this->assertContains('whoami', $applicationTester->getDisplay());

		// Check shunt task life-cycle
		$applicationTester->run(array('command' => 'whoami', 'host' => 'mirror'));
		$this->assertContains('localhost > shunt', $applicationTester->getDisplay());

		// Invalid shunt task
		$this->setExpectedException('InvalidArgumentException', 'Invalid host:non-exists-host');
		$applicationTester->run(array('command' => 'whoami', 'host' => 'non-exists-host'));
    }
}