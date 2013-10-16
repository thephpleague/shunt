<?php

/*
 * This file is part of Shunt.
 *
 * (c) Taufan Aditya <toopay@taufanaditya.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Shunt\Contracts;

/**
 * Shunt SFTP Interface
 *
 * @author Taufan Aditya <toopay@taufanaditya.com>
 */
interface SFTPInterface
{
    /**
     * Attempts to change the mode of the specified file to that given in mode.
     *
     * @param  string $filename
     * @param  int    $mode
     * @return bool
     */
    public function chmod($filename = '', $mode = 0644);

    /**
     * Stats a symbolic link on the remote filesystem without following the link.
     *
     * @param  string $path
     * @return array
     */
    public function lstat($path = '');

    /**
     * Stats a file on the remote filesystem following any symbolic links.
     *
     * @param  string $path
     * @return array
     */
    public function stat($path = '');

    /**
     * Creates a directory on the remote file server with permissions set to mode.
     *
     * @param  string $dirname
     * @param  int    $mode
     * @param  bool   $recursive
     * @return bool
     */
    public function mkdir($dirname = '', $mode = 0777, $recursive = false);

     /**
     * Removes a directory from the remote file server.
     *
     * @param  string $dirname
     * @return bool
     */
    public function rmdir($dirname = '');

    /**
     * Creates a symbolic link named link on the remote filesystem pointing to target.
     *
     * @param  string $target
     * @param  string $link
     * @return bool
     */
    public function symlink($target = '',$link = '');

    /**
     * Returns the target of a symbolic link.
     *
     * @param  string $link
     * @return string
     */
    public function readlink($link = '');

    /**
     * Translates filename into the effective real path on the remote filesystem.
     *
     * @param  string $filename
     * @return string
     */
    public function realpath($filename = '');

    /**
     * Renames a file on the remote filesystem.
     *
     * @param  string $from
     * @param  string $to
     * @return bool
     */
    public function rename($from = '', $to = '');

    /**
     * Deletes a file on the remote filesystem.
     *
     * @param  string $filename
     * @return bool
     */
    public function unlink($filename = '');
}
