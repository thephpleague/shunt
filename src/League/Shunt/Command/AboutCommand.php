<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Command to display information about Shunt
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class AboutCommand extends BaseCommand
{
    /**
     * @{inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('about')
            ->setDescription('Short information about Shunt')
            ->setHelp(<<<EOT
<info>vendor/bin/shunt about</info>
EOT
            )
        ;
    }

    /**
     * @{inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(<<<EOT
<info>Shunt</info>
<comment>Shunt is PHP library for executing commands in parallel on multiple remote machines, via SSH</comment>
EOT
        );

    }
}
