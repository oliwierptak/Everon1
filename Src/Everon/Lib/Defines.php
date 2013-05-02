<?php
namespace Everon;

//@todo: remove this file, replace it with env class or something

if (!defined('ev_OS')) {
    if (substr(PHP_OS, 0, 3) == 'WIN') {
        define('ev_OS_UNIX', false);
        define('ev_OS', 'win');
    } else {
        define('ev_OS_UNIX', true);
        define('ev_OS', 'unix');
    }
}

//todo: remove this file

if (!defined('ev_DS')) {
    define('ev_DS', DIRECTORY_SEPARATOR);
}

if (!defined('ev_DIR_ROOT')) {
    $nesting = implode('..', array_fill(0, 4, ev_DS));
    define('ev_DIR_ROOT', realpath(dirname(__FILE__).$nesting).ev_DS);
}

if (!defined('ev_DIR_CONFIG')) {
    define('ev_DIR_CONFIG', ev_DIR_ROOT.'Config'.ev_DS);
}

if (!defined('ev_DIR_CONTROLLER')) {
    define('ev_DIR_CONTROLLER', ev_DIR_ROOT.'Controller'.ev_DS);
}

if (!defined('ev_DIR_MODEL')) {
    define('ev_DIR_MODEL', ev_DIR_ROOT.'Model'.ev_DS);
}

if (!defined('ev_DIR_SRC')) {
    define('ev_DIR_SRC', ev_DIR_ROOT.'Src'.ev_DS);
}
if (!defined('ev_DIR_EVERON')) {
    define('ev_DIR_EVERON', ev_DIR_SRC.'Everon'.ev_DS);
}
if (!defined('ev_DIR_EVERON_LIB')) {
    define('ev_DIR_EVERON_LIB', ev_DIR_EVERON.'Lib'.ev_DS);
}

if (!defined('ev_DIR_TESTS')) {
    define('ev_DIR_TESTS', ev_DIR_ROOT.'Tests'.ev_DS);
}

if (!defined('ev_DIR_TMP')) {
    define('ev_DIR_TMP', ev_DIR_ROOT.'Tmp'.ev_DS);
}
if (!defined('ev_DIR_CACHE')) {
    define('ev_DIR_CACHE', ev_DIR_TMP.'cache'.ev_DS);
}
if (!defined('ev_DIR_CACHE_CONFIG')) {
    define('ev_DIR_CACHE_CONFIG', ev_DIR_CACHE.'config'.ev_DS);
}
if (!defined('ev_DIR_LOG')) {
    define('ev_DIR_LOG', ev_DIR_TMP.'logs'.ev_DS);
}

if (!defined('ev_DIR_VIEW')) {
    define('ev_DIR_VIEW', ev_DIR_ROOT.'View'.ev_DS);
}

if (!defined('ev_DIR_VIEW_TEMPLATES')) {
    define('ev_DIR_VIEW_TEMPLATES', ev_DIR_VIEW.'Templates'.ev_DS);
}