<?php
/**
 * This file contains overridden FTP functions and a static Spy class.
 *
 * We override the build in PHP FTP functions by defining them within the same
 * namespace as the class who is calling them.
 */

namespace Amplexor\XConnect\Service;

function ftp_connect($host, $port = 21, $timeout = 90)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());
    return ($host !== 'connection.fail');
}

function ftp_login($connection, $user, $pass)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());
    return ($user !== 'login.fail');
}

function ftp_pasv($connection, $passive)
{

}

function ftp_put($connection, $remoteFile, $localFile, $mode)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());

    $remoteParts = explode('/', $remoteFile);
    $remote = array_pop($remoteParts);
    $localParts = explode('/', $localFile);
    $local = array_pop($localParts);

    return ($remote !== 'fail.zip' && $local !== 'fail.zip');
}

function ftp_nlist($connection, $directory)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());

    $list = ['.', '..'];

    if ($directory === 'withDirectories') {
        $list[] = 'directory_1';
        $list[] = 'directory_2';
    }

    if ($directory === 'withFiles') {
        $list[] = 'response1.zip';
        $list[] = 'response2.zip';
    }

    // Default empty directory.
    return $list;
}

function ftp_size($connection, $file)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());

    // Directories return -1.
    if (preg_match('#[\.]{1,2}$#', $file)) {
        return -1;
    }
    if (preg_match('#directory?#', $file)) {
        return -1;
    }

    // Random file size.
    return rand(1, 3000);
}

function ftp_get($connection, $localFile, $remoteFile, $mode)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());

    $remoteParts = explode('/', $remoteFile);
    $remote = array_pop($remoteParts);
    $localParts = explode('/', $localFile);
    $local = array_pop($localParts);

    return ($remote !== 'fail.zip' && $local !== 'fail.zip');
}

function ftp_rename($connection, $from, $to)
{
    FtpServiceTestSpy::log(__FUNCTION__, func_get_args());

    $fromParts = explode('/', $from);
    $fromFile = array_pop($fromParts);
    $toParts = explode('/', $to);
    $toFile = array_pop($toParts);

    return ($fromFile !== 'fail.zip' && $toFile !== 'fail.zip');
}



/**
 * SpyClass to log ftp function calls.
 */
class FtpServiceTestSpy
{
    private static $log = [];

    /**
     * Log activity.
     *
     * @param string $function
     *   The called function.
     * @param array $args
     *   The function arguments.
     */
    public static function log($function, $args)
    {
        $key = static::functionName($function);
        self::$log[$key] = $args;
    }

    /**
     * Get the log for the given function.
     *
     * @param string $function
     *   The function name
     *
     * @return array
     *   The arguments.
     */
    public static function getLog($function)
    {
        $key = static::functionName($function);
        if (!array_key_exists($key, self::$log)) {
            return false;
        }

        return self::$log[$key];
    }

    public static function allLog()
    {
        return self::$log;
    }

    /**
     * Clear the log.
     */
    public static function reset()
    {
        self::$log = [];
    }

    /**
     * Get the function name without namespace.
     *
     * @param string $function
     *   The namespaced function name.
     *
     * @return string
     *   The function name without prefix.
     */
    protected static function functionName($function)
    {
        $parts = explode('\\', $function);
        return array_pop($parts);
    }
}
