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


    function __construct($root, $everon_source_root)
    {
        $this->resources = [
            'root' => $root,
            'everon_source_root' => $everon_source_root
        ];

        $this->resources += [
            'config' => $this->getRoot().'Config'.DIRECTORY_SEPARATOR,
            'controller' => $this->getRoot().'Controller'.DIRECTORY_SEPARATOR,
            'data_mapper' => $this->getRoot().'DataMapper'.DIRECTORY_SEPARATOR,
            'domain' => $this->getRoot().'Domain'.DIRECTORY_SEPARATOR,
            'module' => $this->getRoot().'Module'.DIRECTORY_SEPARATOR,
            'tests' => $this->getRoot().'Tests'.DIRECTORY_SEPARATOR,
            'tmp' => $this->getRoot().'Tmp'.DIRECTORY_SEPARATOR,
            'rest' => $this->getRoot().'Rest'.DIRECTORY_SEPARATOR,
            'web' => $this->getRoot().'Web'.DIRECTORY_SEPARATOR,
            'view' => $this->getRoot().'View'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'assets' => $this->getWeb().'assets'.DIRECTORY_SEPARATOR,
        ];
        
        $this->resources += [
            'everon_config' => $this->getEveronRoot().'Config'.DIRECTORY_SEPARATOR,
            'everon_interface' => $this->getEveronRoot().'Interfaces'.DIRECTORY_SEPARATOR,
            'everon_helper' => $this->getEveronRoot().'Helper'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'cache' => $this->getTmp().'cache'.DIRECTORY_SEPARATOR,
            'log' => $this->getTmp().'logs'.DIRECTORY_SEPARATOR,
        ];

        $this->resources += [
            'cache_config' => $this->getCache().'config'.DIRECTORY_SEPARATOR,
            'cache_view' => $this->getCache().'view'.DIRECTORY_SEPARATOR,
        ];
    }

    //todo: replace all methods with get('Src.Everon.Lib');
    function getRoot()
    {
        return $this->resources['root'];
    }
    
    function setRoot($root)
    {
        $this->resources['root'] = $root;
    }

    function getEveronRoot()
    {
        return $this->resources['everon_source_root'];
    }

    function setEveronRoot($everon_root)
    {
        $this->resources['everon_source_root'] = $everon_root;
    }
    
    function getConfig()
    {
        return $this->resources['config'];
    }
    
    function setConfig($config)
    {
        $this->resources['config'] = $config;
    }

    function getDomain()
    {
        return $this->resources['domain'];
    }
    
    function setDomain($domain)
    {
        $this->resources['domain'] = $domain;
    }

    function getDataMapper()
    {
        return $this->resources['data_mapper'];
    }
    
    function setDataMapper($data_mapper)
    {
        $this->resources['data_mapper'] = $data_mapper;
    }
    
    function getView()
    {
        return $this->resources['view'];
    }
    
    function setView($view)
    {
        $this->resources['view'] = $view;
    }

    function getController()
    {
        return $this->resources['controller'];
    }

    function setController($controller)
    {
        $this->resources['controller'] = $controller;
    }

    function getTest()
    {
        return $this->resources['tests'];
    }
    
    function setTest($test)
    {
        $this->resources['tests'] = $test;
    }

    function getEveronConfig()
    {
        return $this->resources['everon_config'];
    }
    
    function setEveronLib($everon_config)
    {
        $this->resources['everon_config'] = $everon_config;
    }

    function getEveronInterface()
    {
        return $this->resources['everon_interface'];
    }
    
    function setEveronInterface($everon_interfaces)
    {
        $this->resources['everon_interface'] = $everon_interfaces;
    }
    
    function getEveronHelper()
    {
        return $this->resources['everon_helper'];
    }
    
    function setEveronHelper($everon_helper)
    {
        $this->resources['everon_helper'] = $everon_helper;
    }

    function getTmp()
    {
        return $this->resources['tmp'];
    }
    
    function setTmp($tmp)
    {
        $this->resources['tmp'] = $tmp;
    }

    function getCache()
    {
        return $this->resources['cache'];
    }
    
    function setCache($cache)
    {
        $this->resources['cache'] = $cache;
    }

    function getCacheConfig()
    {
        return $this->resources['cache_config'];
    }
    
    function setCacheConfig($cache_config)
    {
        $this->resources['cache_config'] = $cache_config;
    }

    function getCacheView()
    {
        return $this->resources['cache_view'];
    }

    function setViewCache($view_cache)
    {
        $this->resources['cache_view'] = $view_cache;
    }

    function getLog()
    {
        return $this->resources['log'];
    }
    
    function setLog($log)
    {
        $this->resources['log'] = $log;
    }

    function getWeb()
    {
        return $this->resources['web'];
    }

    function setWeb($web)
    {
        $this->resources['web'] = $web;
    }
    
    function getAssets()
    {
        return $this->resources['assets'];
    }
    
    function setAssets($assets)
    {
        $this->resources['assets'] = $assets;
    }
    
    function getModule()
    {
        return $this->resources['module'];
    }
    
    function setModule($module)
    {
        $this->resources['module'] = $module;
    }

    function getRest()
    {
        return $this->resources['rest'];
    }

    function setRest($rest)
    {
        $this->resources['rest'] = $rest;
    }
    
    function toArray()
    {
        return $this->resources;
    }
    
}