#Shunt

[![Build Status](https://secure.travis-ci.org/php-loep/shunt.png?branch=master)](http://travis-ci.org/php-loep/shunt) [![Dependencies Status](https://d2xishtp1ojlk0.cloudfront.net/d/6064030)](http://depending.in/php-loep/shunt) [![Coverage Status](https://coveralls.io/repos/php-loep/shunt/badge.png?branch=master)](https://coveralls.io/r/php-loep/shunt?branch=master) [![Latest Stable Version](https://poser.pugx.org/league/shunt/v/stable.png)](https://packagist.org/packages/league/shunt) [![Total Downloads](https://poser.pugx.org/league/shunt/downloads.png)](https://packagist.org/packages/league/shunt)

Inspired by Ruby's Capistrano, Shunt is PHP library for executing commands in parallel on multiple remote machines, via SSH. Specifically, this library was written to simplify and automate deployment of PHP applications to distributed environments.

## Install

Via Composer

    {
        "require": {
            "league/shunt": "~2.0"
        }
    }
    
## Requirement

* PHP >= 5.3.3
* libssh2
* ssh2.so

## Assumptions

As opinionated as Ruby's Capistrano, Shunt has very firm ideas about how things ought to be done, and tries to force those ideas on you. Some of the assumptions behind these opinions are:

* You are using SSH to access the remote servers.
* You either have the same password to all target machines, or you have public keys in place to allow passwordless access to them.

Do not expect these assumptions to change.

## Usage
In general, you'll use Shunt as follows:

* Create a recipe file (`Shuntfile`).
* Use the `shunt` script to execute your recipe.

From the root folder of your composer-based project, use the Shunt script as follows:

	vendor/bin/shunt some_task some_host,other_host

By default, the script will look for a file called `Shuntfile`, which contain hosts information, credential and your tasks. Here the structure of `Shuntfile` :

	<?php

	return array(

		'hosts' => array(
			'staging' => 'staging.domain.com'
			'repro' => 'backup.domain.com'
			'production' => 'production.domain.com'
		),

		'auth' => array(
			'username' => 'shunt',
			'password' => 'hearmyroar',
			'pubkeyfile' => NULL,
			'privkeyfile' => NULL,
			'passphrase' => NULL,
		),

		'tasks' => array(
			'read_home_dir' => function($s) {
				$s->run('ls');
			},
			'print_php_info' => function($s) {
				$s->run('php -i');
			}
		),
	);

The `tasks` collection indicates which tasks that available to execute. Based by above recipe, you could run :

	vendor/bin/shunt read_home_dir *

Above command will execute `ls` on all remote machines defined in `hosts` parameter. You could tell Shunt to run the task on specific host(s) by appending the host nickname right after the task :

	vendor/bin/shunt read_home_dir staging
	vendor/bin/shunt print_php_info staging,production

Shunt also will automatically create some abbreviation for your task. You can execute `list` commant to see all the available tasks and available hosts.

Changelog
---------

[See the changelog file](https://github.com/php-loep/shunt/blob/master/CHANGELOG.md)

Contributing
------------

Please see [CONTRIBUTING](https://github.com/php-loep/shunt/blob/master/CONTRIBUTING.md) for details.

Support
-------

Bugs and feature request are tracked on [GitHub](https://github.com/php-loep/shunt/issues)


License
-------

Shunt is released under the MIT License. See the bundled
[LICENSE](https://github.com/php-loep/shunt/blob/master/LICENSE) file for details.
