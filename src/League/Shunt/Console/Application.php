<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Console;

use League\Shunt\Contracts\ApplicationInterface;
use League\Shunt\Command;
use League\Shunt\Auth;
use League\Shunt\Session;
use League\Shunt\Shunt;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use ReflectionFunction;
use Closure;
use InvalidArgumentException;

/**
 * The console application that handles the commands
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class Application extends BaseApplication implements ApplicationInterface
{
    /**
     * @var bool
     */
    private $inTest = false;

    /**
     * @var ShuntInterface
     */
    protected $shunt;

    /**
     * @var string Recipe array
     */
    protected $recipes = array();

    /**
     * @var array Collected hosts information
     */
    protected $hosts = array('local' => 'localhost');

    /**
     * @var array Collected authentication information
     */
    protected $auth = array('auth_none' => array('username' => 'root'));

    /**
     * @var array Collected tasks information
     */
    protected $tasks = array();

    /**
     * Constructor
     *
     * @param array Shunt Recipes
     * @param bool  Whether to run as test or not
     */
    public function __construct($recipes = array(), $inTest = false)
    {
        if (function_exists('ini_set')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);

        }
        if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
            date_default_timezone_set(@date_default_timezone_get());
        }

        Shunt::registerErrorHandler();

        // Set initial params
        $this->inTest = $inTest;
        if ( ! empty($recipes)) {
            $this->recipes = $recipes;
        }

        // Collect data from recipe
        $this->collectData();

        parent::__construct('Shunt', Shunt::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return ($this->inTest) ? $this->doRun($input, $output) : parent::run($input, $output);
    }

    /**
     * @{inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // @codeCoverageIgnoreStart
        if (true === $input->hasParameterOption(array('--version', '-V'))) {
            $output->writeln($this->getLongVersion());

            return 0;
        }

        $name = $this->getCommandName($input);
        if (true === $input->hasParameterOption(array('--help', '-h'))) {
            if (!$name) {
                $name = 'help';
                $input = new ArrayInput(array('command' => 'help'));
            } else {
                $this->wantHelps = true;
            }
        }

        if (!$name) {
            $name = 'list';
            $input = new ArrayInput(array('command' => 'list'));
        }
        // @codeCoverageIgnoreEnd

        // the command name MUST be the first element of the input
        $command = $this->find($name);
        $this->runningCommand = $command;

        if (array_key_exists($name, $this->getTasks())) {
            // Get hosts
            $hosts = $this->getHostNames($input);

            // Mark start
            $start = array(microtime(true),memory_get_usage());

            foreach ($hosts as $no => $host) {
                $output->writeln('<info>#'.($no+1).'. Running "'.$name.'" task on "'.$host.'"</info>');

                // Build the Shunt runner
                $session = new Session($this->getHost($host));
                $auth = new Auth($this->getAuth());
                $this->shunt = new Shunt($session, $auth, $output);

                $exitCode = $this->doRunCommand($command, $input, $output);
                $output->writeln('<info>Done.</info>');
                $output->writeln('');
            }

            // Mark End
            $end = array(microtime(true),memory_get_usage());

            $output->writeln('<info>All tasks done.</info>');
            $output->writeln('Time: '.($end[0]-$start[0]).' seconds, Memory: '.($end[1]-$start[1]).' bytes');

            $this->runningCommand = null;
        } else {
            // Not a Shunt task...
            $exitCode = $this->doRunCommand($command, $input, $output);
            $this->runningCommand = null;
        }

        return $exitCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getHelp()
    {
        $messages = array(
            $this->getLongVersion(),
            '',
            '<comment>Usage:</comment>',
            '  [options] command [host]',
            '',
            '<comment>Options:</comment>',
        );

        foreach ($this->getDefinition()->getOptions() as $option) {
            $messages[] = sprintf('  %-29s %s %s',
                '<info>--'.$option->getName().'</info>',
                $option->getShortcut() ? '<info>-'.$option->getShortcut().'</info>' : '  ',
                $option->getDescription()
            );
        }

        $messages[] = '';
        $messages[] = '<comment>Available hosts:</comment>';

        foreach ($this->getHosts() as $nick => $hostname) {
            $messages[] = sprintf('  %-29s %s',
                '<info>'.$nick.'</info>',
                $hostname.($nick == 'local' ? ' (default)' : '')
            );
        }

        return implode(PHP_EOL, $messages);
    }

    /**
     * @{inheritDoc}
     */
    public function getShunt()
    {
        return $this->shunt;
    }

    /**
     * @{inheritDoc}
     */
    public function collectData()
    {
        $recipes = $this->recipes;

        // @codeCoverageIgnoreStart
        if (empty($recipes)) {
            $currentDir = (isset($_SERVER['PWD'])) ? realpath($_SERVER['PWD']) : getcwd();

            if (($holder = $currentDir . DIRECTORY_SEPARATOR . Shunt::holder()) && @file_exists($holder)) {
                $recipes = include $holder;
            }
        }
        // @codeCoverageIgnoreEnd

        // Set the hosts
        if (array_key_exists(Shunt::KEY_HOSTS, $recipes)) {
            foreach ($recipes[Shunt::KEY_HOSTS] as $nickServer => $server) {
                $this->setHost($nickServer, $server);
            }
        }

        // Set the auth credential
        if (array_key_exists(Shunt::KEY_AUTH, $recipes)) {
            $credential = array_filter($recipes[Shunt::KEY_AUTH]);
            // @codeCoverageIgnoreStart
            if (array_key_exists(Shunt::KEY_AUTH_PASSWORD, $credential)) {
                $this->setAuth(Auth::PASSWORD, $credential);
            } elseif (array_key_exists(Shunt::KEY_AUTH_PUBKEY, $credential) && array_key_exists(Shunt::KEY_AUTH_PRIVKEY, $credential)) {
                $this->setAuth(Auth::PUBKEY_FILE, $credential);
            }
            // @codeCoverageIgnoreEnd
        }

        // Set the available tasks
        if (array_key_exists(Shunt::KEY_TASKS, $recipes)) {
            foreach ($recipes[Shunt::KEY_TASKS] as $taskName => $taskFunction) {
                // Validate each task
                if ($taskFunction instanceof Closure) {
                    $taskInformation = ReflectionFunction::export($recipes[Shunt::KEY_TASKS][$taskName], TRUE);

                    if (strpos($taskInformation, Shunt::SIGNATURE_VAR) !== FALSE ||
                        strpos($taskInformation, Shunt::SIGNATURE_CLASS) !== FALSE ||
                        strpos($taskInformation, Shunt::SIGNATURE_NAMESPACE_CLASS) !== FALSE
                    ) {
                        $this->setTask($taskName, new ReflectionFunction($taskFunction));
                    }
                }
            }
        }
    }

    /**
     * @{inheritDoc}
     */
    public function setHost($nickname, $hostname)
    {
        $this->hosts[$nickname] = $hostname;
    }

    /**
     * @{inheritDoc}
     */
    public function getHost($nickname)
    {
        return $this->hosts[$nickname];
    }

    /**
     * @{inheritDoc}
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * @{inheritDoc}
     */
    public function setAuth($type, $data)
    {
        $this->auth[$type] = $data;
    }

    /**
     * @{inheritDoc}
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @{inheritDoc}
     */
    public function setTask($name, $reflector)
    {
        $verbose = ucfirst(str_replace('_', ' ', $name));

        $this->tasks[$name] = compact('name', 'verbose', 'reflector');
    }

    /**
     * @{inheritDoc}
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @{inheritDoc}
     */
    public function getHostNames(InputInterface $input)
    {
        $hosts = array('local');

        $input->bind($this->getDefaultInputDefinition());

        if ($input->hasArgument('host')) {
            $hosts = array();
            $nicknames = explode(',', $input->getArgument('host'));
            foreach ($nicknames as $nick) {
                if ($nick == 'all' || $nick == '.') {
                    // Get all hosts
                    $hosts = array_keys($this->getHosts());
                    break;
                } elseif (array_key_exists($nick, $this->getHosts())) {
                    $hosts[] = $nick;
                } else {
                    throw new InvalidArgumentException('Invalid host:'.$nick);
                }
            }
        }

        return $hosts;
    }

    /**
     * @{inheritDoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\AboutCommand();

        // Register all collected tasks
        foreach ($this->getTasks() as $task) {
            $commands[] = new Command\ArbitraryCommand($task['name'], $task['verbose'], $task['reflector']);
        }

        return $commands;
    }

    /**
     * @{inheritDoc}
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

            new InputArgument('host', InputArgument::OPTIONAL, 'The target host(s)'),

            new InputOption('--help',           '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--quiet',          '-q', InputOption::VALUE_NONE, 'Do not output any message.'),
            new InputOption('--verbose',        '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
            new InputOption('--version',        '-V', InputOption::VALUE_NONE, 'Display this application version.'),
            new InputOption('--ansi',           '',   InputOption::VALUE_NONE, 'Force ANSI output.'),
            new InputOption('--no-ansi',        '',   InputOption::VALUE_NONE, 'Disable ANSI output.'),
            new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question.'),
        ));
    }
}
