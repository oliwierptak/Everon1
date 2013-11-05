<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces;

use Everon\Interfaces;

interface ConfigManager
{
    function register(Config $Config);
    function unRegister($name);
    function isRegistered($name);
    
    /**
     * @param $name
     * @return Interfaces\Config
     */    
    function getConfigByName($name);
    function getApplicationConfig();
    function getRouterConfig();
    function getViewConfig();
    function getConfigs();
}
