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

use Everon\Exception;
use Everon\Interfaces;

interface ConfigManager
{
    /**
     * @return Interfaces\ConfigLoader
     */
    function getConfigLoader();

    /**
     * @param Interfaces\ConfigLoader $ConfigLoader
     */
    function setConfigLoader(Interfaces\ConfigLoader $ConfigLoader);
        
    /**
     * @return Interfaces\Config
     */
    function getDatabaseConfig();

    /**
     * @return array
     */
    function getEnvironmentExpressions();

    /**
     * @return Interfaces\ConfigExpressionMatcher
     */
    function getExpressionMatcher();

    /**
     * @return bool
     */
    function isCachingEnabled();

    /**
     * @param bool $caching_enabled
     */
    function setIsCachingEnabled($caching_enabled);

    /**
     * @param Interfaces\Config $Config
     * @throws Exception\Config
     */
    function register(Interfaces\Config $Config);

    /**
     * @param $config_name
     * @param $filename
     */
    function registerByFilename($config_name, $filename);

    /**
     * @param $name
     */
    function unRegister($name);

    /**
     * @param $name
     * @return bool
     */
    function isRegistered($name);

    /**
     * @return array|null
     */
    function getConfigs();

    /**
     * @param $name
     * @return Interfaces\Config
     */
    function getConfigByName($name);

    /**
     * @param $expression
     * @return mixed|null
     */
    function getConfigValue($expression);    
}
