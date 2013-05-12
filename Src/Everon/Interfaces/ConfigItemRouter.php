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


interface ConfigItemRouter
{
    function getName();
    function setName($route_name);
    function getUrl();
    function setUrl($url);
    function getController();
    function setController($controller);
    function getAction();
    function setAction($action);
    function isDefault();
    function setIsDefault($is_default);

    function getGetRegex();
    function setGetRegex($regex);
    function getPostRegex();
    function setPostRegex($regex);

    function replaceCurlyParametersWithRegex($pattern, array $data);
    function getCleanUrl($str, $marker='?');
    function filterQueryKeys($get_data);
    function filterGetKeys($get_data);
    function matchesByUrl($request_url);
    
    function toArray();
}