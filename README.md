#Shunt

[![Teaser](https://i.imgur.com/isgocwl.png?1)](https://github.com/thephpleague/shunt)

[![Build Status](https://secure.travis-ci.org/thephpleague/shunt.png?branch=master)](http://travis-ci.org/thephpleague/shunt) [![Dependencies Status](https://depending.in/thephpleague/shunt.png)](https://depending.in/thephpleague/shunt) [![Coverage Status](https://coveralls.io/repos/thephpleague/shunt/badge.png?branch=master)](https://coveralls.io/r/thephpleague/shunt?branch=master) [![Latest Stable Version](https://poser.pugx.org/league/shunt/v/stable.png)](https://packagist.org/packages/league/shunt) [![Total Downloads](https://poser.pugx.org/league/shunt/downloads.png)](https://packagist.org/packages/league/shunt)

Inspired by Ruby's Capistrano, Shunt is PHP library for executing commands on multiple remote machines, via SSH. Specifically, this library was written to simplify and automate deployment of PHP applications to distributed environments.

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

## Additional Features

* Secure copy (SCP) support
* Secure FTP (SFTP) support

## Assumptions

Shunt has very firm ideas about how things ought to be done, and tries to force those ideas on you. Some of the assumptions behind these opinions are:

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

```php
<?php

return array(

	'hosts' => array(
		'staging' => 'staging.domain.com',
		'repro' => 'backup.domain.com',
		'production' => 'production.domain.com',
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
		},
		'upload_foo_source' => function($s) {
			$s->sftp()->mkdir('source');
			$s->scp()->put('foo', 'source/foo');
		}
	),
);
```

The `tasks` collection indicates which tasks that available to execute. You can execute `list` command to see all the available tasks and available hosts. Based by above recipes, you could run :

	vendor/bin/shunt read_home_dir .

Above command will execute `ls` on all remote machines defined in `hosts` parameter. You could tell Shunt to run the task on specific host(s) by appending the host nickname right after the task :

	vendor/bin/shunt read_home_dir staging
	vendor/bin/shunt print_php_info staging,production

As you may already notice, you could easily access **SCP** and **SFTP** instance by calling `scp()` or `sftp()` method within your task. Bellow table shows available APIs for both **SCP** and **SFTP** instances :

| Type | Method Signature | Description
| :---: | :---: | :---: |
| **SCP** | `put($localFile = '', $remoteFile = '')` | Send a file from local to remote path |
| **SCP** | `get($remoteFile = '', $localFile = '')` | Get a file from remote to local path |
| **SFTP** | `chmod($filename = '', $mode = 0644)` | Attempts to change the mode of the specified file to that given in mode. |
| **SFTP** | `lstat($path = '')` | Stats a symbolic link on the remote filesystem without following the link. |
| **SFTP** | `stat($path = '')` | Stats a file on the remote filesystem following any symbolic links. |
| **SFTP** | `mkdir($dirname = '', $mode = 0777, $recursive = false)` | Creates a directory on the remote file server with permissions set to mode. |
| **SFTP** | `rmdir($dirname = '')` | Removes a directory from the remote file server. |
| **SFTP** | `symlink($target = '',$link = '')` | Creates a symbolic link named link on the remote filesystem pointing to target. |
| **SFTP** | `readlink($link = '')` | Returns the target of a symbolic link. |
| **SFTP** | `realpath($filename = '')` | Translates filename into the effective real path on the remote filesystem. |
| **SFTP** | `rename($from = '', $to = '')` | Renames a file on the remote filesystem. |
| **SFTP** | `unlink($filename = '')` | Deletes a file on the remote filesystem. |


Changelog
---------

[See the changelog file](https://github.com/thephpleague/shunt/blob/master/CHANGELOG.md)

Contributing
------------

Please see [CONTRIBUTING](https://github.com/thephpleague/shunt/blob/master/CONTRIBUTING.md) for details.

Support
-------

Bugs and feature request are tracked on [GitHub](https://github.com/thephpleague/shunt/issues)


License
-------

Shunt is released under the MIT License. See the bundled
[LICENSE](https://github.com/thephpleague/shunt/blob/master/LICENSE) file for details.
