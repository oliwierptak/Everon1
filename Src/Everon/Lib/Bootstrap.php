<?php
namespace Everon
{
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Defines.php');
    require_once(ev_DIR_EVERON_LIB . 'General.php');

    require_once(ev_DIR_EVERON.'Interfaces'.DIRECTORY_SEPARATOR.'ClassLoader.php');
    require_once(ev_DIR_EVERON.'ClassLoader.php');

    $cache = false;
    $ini = @parse_ini_file(ev_DIR_CONFIG.'application.ini', true);
    if (is_array($ini) && array_key_exists('cache', $ini) && array_key_exists('autoloader', $ini['cache'])) {
        $cache = (bool) $ini['cache']['autoloader'];
    }

    $ClassMap = null;
    if ($cache) {
        require_once(ev_DIR_EVERON.'Interfaces'.DIRECTORY_SEPARATOR.'ClassMap.php');
        require_once(ev_DIR_EVERON.'ClassMap.php');
        $ClassMap = new ClassMap();
    }

    $ClassLoader = new ClassLoader($ClassMap);
    $ClassLoader->unRegister();

    $ClassLoader->add('Everon', ev_DIR_SRC);
    $ClassLoader->add('Everon\Model', ev_DIR_MODEL);
    $ClassLoader->add('Everon\View', ev_DIR_VIEW);
    $ClassLoader->add('Everon\Controller', ev_DIR_CONTROLLER);
    $ClassLoader->register();

    require_once(ev_DIR_EVERON.'Exception/Exception.php');
    require_once(ev_DIR_EVERON.'Exception/Repository.php');

    if (is_file(ev_DIR_SRC.'Exception.php')) {
        require_once(ev_DIR_SRC.'Exception.php');
    }

    //set_error_handler('\ev_exceptionLogger');

    

}
