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

        $Environment = new \Everon\Environment(PROJECT_ROOT);

        require_once($Environment->getEveronInterface().'ClassLoader.php');
        require_once($Environment->getEveron().'ClassLoader.php');
        
        $use_cache = false;
        $ini = @parse_ini_file($Environment->getConfig().'application.ini', true);
        if (is_array($ini) && array_key_exists('cache', $ini) && array_key_exists('autoloader', $ini['cache'])) {
            $use_cache = (bool) $ini['cache']['autoloader'];
        }

        $ClassMap = null;
        if ($use_cache) {
            require_once($Environment->getEveron().'ClassLoaderCache.php');
            require_once($Environment->getEveronInterface().'ClassMap.php');
            require_once($Environment->getEveron().'ClassMap.php');
            
            $classmap_filename = $Environment->getCache().'everon_classmap_'.ev_OS.'.php';
            $ClassMap = new ClassMap($classmap_filename);
            $ClassLoader = new ClassLoaderCache($ClassMap);
        }
        else {
            $ClassLoader = new ClassLoader();    
        }
        
        $ClassLoader->add('Everon', $Environment->getSource());
        $ClassLoader->add('Everon\Model', $Environment->getModel());
        $ClassLoader->add('Everon\View', $Environment->getView());
        $ClassLoader->add('Everon\Controller', $Environment->getController());
        $ClassLoader->register();

        require_once($Environment->getEveron().'Exception/Exception.php');
        require_once($Environment->getEveron().'Exception/Repository.php');

        //set_error_handler('\ev_exceptionLogger');
    }

    run();
}
