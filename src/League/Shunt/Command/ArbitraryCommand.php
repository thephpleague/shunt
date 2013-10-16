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
use ReflectionFunction;

/**
 * Command that act as a shell for registered task within Shuntfile
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class ArbitraryCommand extends BaseCommand
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @var ReflectionFunction
     */
    protected $callable;

    /**
     * Constructor.
     *
     * @param string             $name        The name of the command
     * @param string             $description The description of the command
     * @param ReflectionFunction $callable    The callback of the command
     *
     * @throws \LogicException When the command name is empty
     *
     * @api
     */
    public function __construct($name, $description, ReflectionFunction $callable)
    {
        $this->description = $description;
        $this->callable = $callable;

        parent::__construct($name);
    }

    /**
     * @{inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName($this->getName())
            ->setDescription($this->description)
            ->setHelp('<info>vendor/bin/shunt '.$this->getName().'</info>')
        ;
    }

    /**
     * @{inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln('Running '.$this->getName());
        }

        $this->callable->invoke($this->getApplication()->getShunt());
    }
}
