#Grunt

[![Build Status](https://secure.travis-ci.org/php-loep/grunt.png?branch=master)](http://travis-ci.org/php-loep/grunt) [![Dependencies Status](https://d2xishtp1ojlk0.cloudfront.net/d/6064030)](http://depending.in/php-loep/grunt)
[![Total Downloads](https://poser.pugx.org/league/grunt/downloads.png)](https://packagist.org/packages/league/grunt)
[![Latest Stable Version](https://poser.pugx.org/league/grunt/v/stable.png)](https://packagist.org/packages/league/grunt)

Inspired by Ruby's Capistrano, Grunt is PHP library for executing commands in parallel on multiple remote machines, via SSH. Specifically, this library was written to simplify and automate deployment of PHP applications to distributed environments.

## Install

Via Composer

    {
        "require": {
            "league/grunt": "1.0.*"
        }
    }
    
## Requirement

* PHP >= 5.3.3
* libssh2
* ssh2.so

## Assumptions

As opiniated as Ruby's Capistrano, Grunt has very firm ideas about how things ought to be done, and tries to force those ideas on you. Some of the assumptions behind these opinions are:

* You are using SSH to access the remote servers.
* You either have the same password to all target machines, or you have public keys in place to allow passwordless access to them.

Do not expect these assumptions to change.

## Usage
In general, you'll use Grunt as follows:

* Create a recipe file (`Gruntfile`).
* Use the `grunt` script to execute your recipe.

Use the grunt script as follows:

	grunt --some_task

By default, the script will look for a file called `Gruntfile`, which contain hosts information, credential and your tasks. Here the structure of `Gruntfile` :

	<?php

	return array(

		'hosts' => array(
			'staging' => 'staging.domain.com'
			'repro' => 'backup.domain.com'
			'production' => 'production.domain.com'
		),

		'auth' => array(
			'username' => 'grunt',
			'password' => 'hearmyroar',
			'pubkeyfile' => NULL,
			'privkeyfile' => NULL,
			'passphrase' => NULL,
		),

		'tasks' => array(
			'read_home_dir' => function($g) {
				$g->run('ls');
			},
			'print_php_info' => function($g) {
				$g->run('php -i');
			}
		),
	);

The `tasks` collection indicates which tasks that available to execute. Based by above recipe, you could run :

	grunt --read_home_dir

Above command will execute `ls` on all remote machines defined in `hosts` parameter. You could tell Grunt to run the task on specific host(s) by appending the host nickname right after the task :

	grunt --read_home_dir staging
	grunt --print_php_info staging,production

Grunt also will automatically create some abbreviation for your task. You can do "grunt" to see all the available tasks.

Changelog
---------

[See the changelog file](https://github.com/php-loep/grunt/blob/master/CHANGELOG.md)

Contributing
------------

Please see [CONTRIBUTING](https://github.com/php-loep/grunt/blob/master/CONTRIBUTING.md) for details.

Support
-------

Bugs and feature request are tracked on [GitHub](https://github.com/php-loep/grunt/issues)


License
-------

Grunt is released under the MIT License. See the bundled
[LICENSE](https://github.com/php-loep/grunt/blob/master/LICENSE) file for details.
