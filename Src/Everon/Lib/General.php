<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//todo remove me
if ((new \SplFileInfo('/var/www/Kint/kint.class.php'))->isFile()) {
    require_once('/var/www/Kint/kint.class.php');
}
    
function mpr()
{
    $args = func_get_args();
    foreach ($args as $a) {
        _mpr($a);
    }
}
/**
 * General purpose debug dumper
 *
 * @param mixed $val the thing to dump
 * @param bool $die
 * @param int $traceIndex
 */
function _mpr($val, $die=false) 
{
    $is_pre = true;
    if (php_sapi_name() == 'cli') {
        $is_pre = false;
    }
    else {
        echo '<pre>';
    }

    if (is_array($val) || is_object($val)) {
        print_r($val);

        if(is_array($val)) {
            reset($val);
        }
    }
    else {
        var_dump($val);
    }

    if ($is_pre) {
        echo '</pre>';
    }

    if ($die) {
        die();
    }
}

function setup($guid_value, $root, $log_filename)
{
    $log_directory = implode(DIRECTORY_SEPARATOR, [$root, 'Tmp', 'logs']).DIRECTORY_SEPARATOR;
    $filename = implode(DIRECTORY_SEPARATOR, [$root, 'Config']).DIRECTORY_SEPARATOR.'application.ini';

    $filename = @parse_ini_file($filename, true);
    if (isset($filename['logger']) && isset($filename['logger']['directory'])) {
        $path = [dirname(__FILE__), '..', '..', '..'];
        $tokens = explode('/', $filename['logger']['directory']);
        $path = array_merge($path, $tokens);
        $log_directory = implode(DIRECTORY_SEPARATOR, $path).DIRECTORY_SEPARATOR;
    }

    $log_filename = $log_directory.$log_filename;
    set_exception_handler(function ($Exception) use ($log_filename, $guid_value) {
        $timestamp = date('c', time());
        error_log((string) $timestamp." ".$Exception."\n", 3, $log_filename);
        header("HTTP/1.1 500 Internal Server Error. Request ID: $guid_value");
    });
    
    require_once(implode(DIRECTORY_SEPARATOR, [$root, 'Src', 'Everon', 'Interfaces']).DIRECTORY_SEPARATOR.'Environment.php');
    require_once(implode(DIRECTORY_SEPARATOR, [$root, 'Src', 'Everon']).DIRECTORY_SEPARATOR.'Environment.php');
    require_once(implode(DIRECTORY_SEPARATOR, [$root, 'Src', 'Everon']).DIRECTORY_SEPARATOR.'Bootstrap.php');    
}