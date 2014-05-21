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

interface ItemRouter extends Item
{
    /**
     * @param string $module
     */
    function setModule($module);

    function getModule();
    
    function getParsedUrl();
    function setParsedUrl($parsed_url);
    function compileUrl($parts);
    
    function getUrl();
    function setUrl($url);
    function getController();
    function setController($controller);
    function getAction();
    function setAction($action);

    function getGetRegex();
    function setGetRegex($regex);
    function getQueryRegex();
    function setQueryRegex($regex);
    function getPostRegex();
    function setPostRegex($regex);

    function replaceCurlyParametersWithRegex($pattern, array $data);
    function getCleanUrl($str, $marker='?');
    function filterQueryKeys($get_data);
    function filterGetKeys($get_data);
    function matchesByPath($request_path);

    /**
     * @param string $method
     */
    function setMethod($method);

    function getMethod();
}