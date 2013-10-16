<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Tests;

use League\Shunt\Auth;
use League\Shunt\Session;
use League\Shunt\Shunt;
use League\Shunt\SFTP;
use Symfony\Component\Console\Output\NullOutput;
use PHPUnit_Framework_TestCase;

/**
 * Shunt SFTP Unit-Test
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
class SFTPTest extends PHPUnit_Framework_TestCase
{
    protected $shunt;
    protected $sftp;

    public function setUp()
    {
        $credential = array('username' => 'shunt', 'password' => 'hearmyroar');
        $session = new Session('localhost');
        $auth = new Auth(array('auth_password' => $credential));
        $output = new NullOutput();

        $this->shunt = new Shunt($session, $auth, $output);
        $this->sftp = $this->shunt->sftp();

        // Ensure we delete stub folder/file
        $this->clean();
    }

    public function tearDown()
    {
        $this->clean();
    }

    public function testFileUtility()
    {
        // Create a file
        $exitCode = $this->shunt->run('touch notsodummyfile', true);
        $this->assertEquals(0, $exitCode);

        // Rename a file
        $renameProcess = $this->sftp->rename('notsodummyfile', 'dummyfile');
        $this->assertTrue($renameProcess);

        // Chmod to 755
        $chmodProcess = $this->sftp->chmod('dummyfile', 0755);
        $this->assertTrue($chmodProcess);

        // Check the stat
        $stat = $this->sftp->stat('dummyfile');
        $this->assertArrayHasKey('size', $stat);
        $this->assertArrayHasKey('uid', $stat);
        $this->assertArrayHasKey('gid', $stat);
        $this->assertArrayHasKey('mode', $stat);
        $this->assertArrayHasKey('atime', $stat);
        $this->assertArrayHasKey('mtime', $stat);

        // Check the lstat
        $lstat = $this->sftp->lstat('dummyfile');
        $this->assertArrayHasKey('size', $lstat);
        $this->assertArrayHasKey('uid', $lstat);
        $this->assertArrayHasKey('gid', $lstat);
        $this->assertArrayHasKey('mode', $lstat);
        $this->assertArrayHasKey('atime', $lstat);
        $this->assertArrayHasKey('atime', $lstat);

        // Check the symlink
        $symlinkProcess = $this->sftp->symlink('dummyfile', 'otherdummyfile');
        $this->assertTrue($symlinkProcess);

        // Get symlink info
        $link = $this->sftp->readlink('otherdummyfile');
        $this->assertEquals('dummyfile', $link);

        // Delete the dummy file
        $deleteProcess = $this->sftp->unlink('dummyfile');
        $deleteProcess = $this->sftp->unlink('otherdummyfile');
        $this->assertTrue($deleteProcess);
    }

    public function testDirectoryUtility()
    {
        // Create a directory
        $createProcess = $this->sftp->mkdir('foo');
        $this->assertTrue($createProcess);

        // Test realpath
        $subdirCreateProcess = $this->sftp->mkdir('foo/bar/and/friends', 0777, true);
        $this->assertTrue($subdirCreateProcess);
        $fileCreateProcessExitCode = $this->shunt->run('cd foo/bar/and/friends; touch foobarfile',true);
        $this->assertEquals(0, $fileCreateProcessExitCode);
        $this->assertContains('foo/bar/and/friends/foobarfile', $this->sftp->realpath('foo/bar/and/friends/foobarfile'));
    }

    private function clean()
    {
        $this->sftp->unlink('dummyfile');
        $this->sftp->unlink('otherdummyfile');
        $this->sftp->unlink('foo/bar/and/friends/foobarfile');
        $this->sftp->rmdir('foo/bar/and/friends');
        $this->sftp->rmdir('foo/bar/and');
        $this->sftp->rmdir('foo/bar');
        $this->sftp->rmdir('foo');
    }
}
