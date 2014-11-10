<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config\Interfaces;

use Everon\Exception;

interface Manager extends 
    \Everon\Interfaces\Dependency\Bootstrap,
    \Everon\Config\Interfaces\Dependency\ConfigLoader,
    \Everon\Interfaces\Dependency\Factory,
    \Everon\Interfaces\Dependency\FileSystem,
    \Everon\Interfaces\Dependency\Logger
{
    /**
     * @return Loader
     */
    function getConfigLoader();

    /**
     * @param Loader $ConfigLoader
     */
    function setConfigLoader(Loader $ConfigLoader);
        
    /**
     * @return \Everon\Interfaces\Config
     */
    function getDatabaseConfig();

    /**
     * @return array
     */
    function getEnvironmentExpressions();

    /**
     * @return ExpressionMatcher
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
     * @param \Everon\Interfaces\Config $Config
     * @throws Exception\Config
     */
    function register(\Everon\Interfaces\Config $Config);

    /**
     * @param $config_name
     * @param $filename
     */
    //function registerByFilename($config_name, $filename);

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
     * @return \Everon\Interfaces\Config
     */
    function getConfigByName($name);

    /**
     * @param \Everon\Interfaces\Config $Config
     */
    function setConfigByName(\Everon\Interfaces\Config $Config);

    /**
     * @param $name
     * @return bool
     */
    function hasConfig($name);

    /**
     * @param $expression
     * @param mixed $default
     * @return mixed|null Returns $default in case nothing was found
     */
    function getConfigValue($expression, $default=null);
}
