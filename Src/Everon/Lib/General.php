<?php

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
function _mpr($val, $die=false, $traceIndex=0) {
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
        $trace = debug_backtrace();
        echo "--\n";
        echo sprintf('Who called me: %s:%s', $trace[0]['file'], $trace[0]['line']);
        if ($traceIndex) {
            echo "\nTrace:";
            for ($i=1; $i<=$traceIndex; $i++) {
                echo sprintf("\n%s:%s", $trace[$i]['file'], $trace[$i]['line']);
            }
        }
        die();
    }
}
