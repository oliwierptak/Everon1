<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

use Everon\Interfaces;

class Environment implements Interfaces\Environment
{

    protected $resources = [];


    public function __construct($root)
    {
        $this->resources = [
            'root' => $root
        ];

        $this->resources += [
            'config' => $this->getRoot().'Config'.DIRECTORY_SEPARATOR,
            'model' => $this->getRoot().'Model'.DIRECTORY_SEPARATOR,
            'view' => $this->getRoot().'View'.DIRECTORY_SEPARATOR,
            'controller' => $this->getRoot().'Controller'.DIRECTORY_SEPARATOR,
            'source' => $this->getRoot().'Src'.DIRECTORY_SEPARATOR,
            'tests' => $this->getRoot().'Tests'.DIRECTORY_SEPARATOR,
            'tmp' => $this->getRoot().'Tmp'.DIRECTORY_SEPARATOR,
            'web' => $this->getRoot().'Web'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'everon' => $this->getSource().'Everon'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'view_template' => $this->getView().'Templates'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'web_cache' => $this->getWeb().'cache'.DIRECTORY_SEPARATOR,
            'web_css' => $this->getWeb().'css'.DIRECTORY_SEPARATOR,
            'web_images' => $this->getWeb().'images'.DIRECTORY_SEPARATOR,
            'web_javascript' => $this->getWeb().'javascript'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'everon_lib' => $this->getEveron().'Lib'.DIRECTORY_SEPARATOR,
            'everon_interface' => $this->getEveron().'Interfaces'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'cache' => $this->getTmp().'cache'.DIRECTORY_SEPARATOR,
            'log' => $this->getTmp().'logs'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'cache_config' => $this->getCache().'config'.DIRECTORY_SEPARATOR,
        ];
    }

    public function getRoot()
    {
        return $this->resources['root'];
    }
    
    public function setRoot($root)
    {
        $this->resources['root'] = $root;
    }

    public function getConfig()
    {
        return $this->resources['config'];
    }
    
    public function setConfig($config)
    {
        $this->resources['config'] = $config;
    }

    public function getModel()
    {
        return $this->resources['model'];
    }
    
    public function setModel($model)
    {
        $this->resources['model'] = $model;
    }

    public function getView()
    {
        return $this->resources['view'];
    }
    
    public function setView($view)
    {
        $this->resources['view'] = $view;
    }

    public function getViewTemplate()
    {
        return $this->resources['view_template'];
    }

    public function setViewTemplate($view_template)
    {
        $this->resources['view_template'] = $view_template;
    }

    public function getController()
    {
        return $this->resources['controller'];
    }

    public function setController($controller)
    {
        $this->resources['controller'] = $controller;
    }

    public function getSource()
    {
        return $this->resources['source'];
    }
    
    public function setSource($source)
    {
        $this->resources['source'] = $source;
    }

    public function getTest()
    {
        return $this->resources['tests'];
    }
    
    public function setTest($test)
    {
        $this->resources['tests'] = $test;
    }

    public function getEveron()
    {
        return $this->resources['everon'];
    }
    
    public function setEveron($everon)
    {
        $this->resources['everon'] = $everon;
    }

    public function getEveronLib()
    {
        return $this->resources['everon_lib'];
    }
    
    public function setEveronLib($everon_lib)
    {
        $this->resources['everon_lib'] = $everon_lib;
    }

    public function getEveronInterface()
    {
        return $this->resources['everon_interface'];
    }
    
    public function setEveronInterface($everon_interfaces)
    {
        $this->resources['everon_interface'] = $everon_interfaces;
    }

    public function getTmp()
    {
        return $this->resources['tmp'];
    }
    
    public function setTmp($tmp)
    {
        $this->resources['tmp'] = $tmp;
    }

    public function getCache()
    {
        return $this->resources['cache'];
    }
    
    public function setCache($cache)
    {
        $this->resources['cache'] = $cache;
    }

    public function getCacheConfig()
    {
        return $this->resources['cache_config'];
    }
    
    public function setCacheConfig($cache_config)
    {
        $this->resources['cache_config'] = $cache_config;
    }

    public function getLog()
    {
        return $this->resources['log'];
    }
    
    public function setLog($log)
    {
        $this->resources['log'] = $log;
    }

    public function getWeb()
    {
        return $this->resources['web'];
    }

    public function setWeb($web)
    {
        $this->resources['web'] = $web;
    }

    public function getWebCache()
    {
        return $this->resources['web_cache'];
    }

    public function setWebCache($web_cache)
    {
        $this->resources['web_cache'] = $web_cache;
    }
}
