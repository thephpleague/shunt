#Grunt

Inspired by Ruby's Capistrano, Grunt is PHP library for executing commands in parallel on multiple remote machines, via SSH. Specifically, this library was written to simplify and automate deployment of PHP applications to distributed environments.

## Requirement

* PHP >= 5.3.3
* libssh2
* ssh2.so

## Assumptions

As Ruby's Capistrano, Grunt is also "opinionated software", which means it has very firm ideas about how things ought to be done, and tries to force those ideas on you. Some of the assumptions behind these opinions are:

* You are using SSH to access the remote servers.
* You either have the same password to all target machines, or you have public keys in place to allow passwordless access to them.

Do not expect these assumptions to change.

## Usage
In general, you'll use Grunt as follows:

* Create a recipe file (`Gruntfile`).
* Use the `grunt` script to execute your recipe.

Use the grunt script as follows:

	grunt -some_task

By default, the script will look for a file called `Gruntfile`, which contain hosts information, credential and your tasks. Here the structure of `Gruntfile` :

	<?php

	return array(

		'hosts' => array(
			'staging' => 'staging.domain.com'
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

	grunt -read_home_dir

Grunt also will automatically create some abbreviation for your task. You can do "grunt" to see all the available tasks.

## License

New BSD (see LICENSE for full license details).