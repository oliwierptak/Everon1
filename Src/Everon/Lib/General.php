<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once('/var/www/Kint/kint.class.php');
    
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
function _mpr($val, $die=false) {
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
