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
    function getConfig();
    function setConfig($config);
    function getModel();
    function setModel($model);
    function getView();
    function setView($view);
    function getViewTemplate();
    function setViewTemplate($view_template);
    function getController();
    function setController($controller);
    function getSource();
    function setSource($source);
    function getTest();
    function setTest($test);
    function getEveron();
    function setEveron($everon);
    function getEveronLib();
    function setEveronLib($everon_list);
    function getEveronInterface();
    function setEveronInterface($everon_interfaces);
    function getTmp();
    function setTmp($tmp);
    function getCache();
    function setCache($cache);
    function getCacheConfig();
    function setCacheConfig($cache_config);
    function getLog();
    function setLog($log);
    function getWebCache();
    function setWebCache($view_template_cache);
    function getWeb();
    function setWeb($web);
}
