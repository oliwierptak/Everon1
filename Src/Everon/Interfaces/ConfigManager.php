<?php
namespace Everon\Interfaces;

use Everon\Interfaces;

interface ConfigManager
{
    function register(Config $Config);
    function unRegister($name);
    
    /**
     * @param $name
     * @return Interfaces\Config
     */    
    function getConfigByName($name);
    function getApplicationConfig();
    function getRouterConfig();
    function getConfigs();
    function enableCache();
    function disableCache();
}
