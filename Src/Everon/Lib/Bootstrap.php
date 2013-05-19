<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon
{
    if (!defined('ev_OS')) {
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            define('ev_OS_UNIX', false);
            define('ev_OS', 'win');
        } else {
            define('ev_OS_UNIX', true);
            define('ev_OS', 'unix');
        }
    }

    //don't pollute namespace
    function run() 
    {
        $nesting = implode('..', array_fill(0, 4, DIRECTORY_SEPARATOR));
        define('PROJECT_ROOT',  realpath(dirname(__FILE__).$nesting).DIRECTORY_SEPARATOR);

        require_once(implode(DIRECTORY_SEPARATOR, [PROJECT_ROOT, 'Src', 'Everon', 'Lib']).DIRECTORY_SEPARATOR.'General.php');
        require_once(implode(DIRECTORY_SEPARATOR, [PROJECT_ROOT, 'Src', 'Everon', 'Interfaces']).DIRECTORY_SEPARATOR.'Environment.php');
        require_once(implode(DIRECTORY_SEPARATOR, [PROJECT_ROOT, 'Src', 'Everon']).DIRECTORY_SEPARATOR.'Environment.php');

        $BootstrapEnvironment = new \Everon\Environment(PROJECT_ROOT);

        if (is_dir($BootstrapEnvironment->getCache()) === false) {
            mkdir($BootstrapEnvironment->getCache(), 0775, true);
        }

        require_once($BootstrapEnvironment->getEveronInterface().'ClassLoader.php');
        require_once($BootstrapEnvironment->getEveron().'ClassLoader.php');

        $cache = false;
        $ini = @parse_ini_file($BootstrapEnvironment->getConfig().'application.ini', true);
        if (is_array($ini) && array_key_exists('cache', $ini) && array_key_exists('autoloader', $ini['cache'])) {
            $cache = (bool) $ini['cache']['autoloader'];
        }

        $ClassMap = null;
        if ($cache) {
            require_once($BootstrapEnvironment->getEveronInterface().'ClassMap.php');
            require_once($BootstrapEnvironment->getEveron().'ClassMap.php');
            $classmap_filename = $BootstrapEnvironment->getCache().'everon_classmap_'.ev_OS.'.php';
            $ClassMap = new ClassMap($classmap_filename);
        }

        $ClassLoader = new ClassLoader($ClassMap);
        $ClassLoader->add('Everon', $BootstrapEnvironment->getSource());
        $ClassLoader->add('Everon\Model', $BootstrapEnvironment->getModel());
        $ClassLoader->add('Everon\View', $BootstrapEnvironment->getView());
        $ClassLoader->add('Everon\Controller', $BootstrapEnvironment->getController());
        $ClassLoader->register();

        require_once($BootstrapEnvironment->getEveron().'Exception/Exception.php');
        require_once($BootstrapEnvironment->getEveron().'Exception/Repository.php');

        if (is_file($BootstrapEnvironment->getSource().'Exception.php')) {
            require_once($BootstrapEnvironment->getSource().'Exception.php');
        }

        //set_error_handler('\ev_exceptionLogger');
    }

    run();
}
