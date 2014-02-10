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


interface Environment
{
    function getRoot();
    function setRoot($root);
    function getEveronRoot();
    function setEveronRoot($everon_root);
    function getConfig();
    function setConfig($config);
    function getDomain();
    function setDomain($domain);
    function getDataMapper();
    function setDataMapper($data_mapper);
    function getView();
    function setView($view);
    function getController();
    function setController($controller);
    function getTest();
    function setTest($test);
    function getEveronConfig();
    function setEveronLib($everon_lib);
    function getEveronInterface();
    function setEveronInterface($everon_interfaces);
    function getEveronHelper();
    function setEveronHelper($everon_helper);    
    function getTmp();
    function setTmp($tmp);
    function getCache();
    function setCache($cache);
    function getCacheConfig();
    function setCacheConfig($cache_config);
    function getCacheView();
    function setViewCache($view_cache);
    function getLog();
    function setLog($log);
    function getWeb();
    function setWeb($web);
    function getModule();
    function setModule($module);
    function toArray();
}
