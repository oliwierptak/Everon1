<?php
namespace Everon
{
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Defines.php');
    require_once(ev_DIR_EVERON_LIB . 'General.php');

    require_once(ev_DIR_EVERON.'Autoloader.php');

    $cache = false;
    $ini = @parse_ini_file(ev_DIR_CONFIG.'application.ini', true);
    if (is_array($ini) && array_key_exists('cache', $ini) && array_key_exists('autoloader', $ini['cache'])) {
        $cache = (bool) $ini['cache']['autoloader'];
    }

    $Autoloader = new Autoloader($cache);
    $Autoloader->loadMap();

    spl_autoload_register(array($Autoloader, 'autoload'));

    require_once(ev_DIR_EVERON.'Exception/Exception.php');
    require_once(ev_DIR_EVERON.'Exception/Repository.php');

    if (is_file(ev_DIR_SRC.'Exception.php')) {
        require_once(ev_DIR_SRC.'Exception.php');
    }

    //set_error_handler('\ev_exceptionLogger');

    

}
