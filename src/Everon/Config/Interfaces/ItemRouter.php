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

    /**
     * @param $parsed_url
     */
    function setParsedUrl($parsed_url);

    /**
     * @param $parts
     */
    function compileUrl($parts);
    
    function getUrl();

    /**
     * @param $url
     */
    function setUrl($url);
    
    function getController();

    /**
     * @param $controller
     */
    function setController($controller);
    
    function getAction();

    /**
     * @param $action
     */
    function setAction($action);

    /**
     * @return array
     */
    function getGetRegex();

    /**
     * @param $regex
     */
    function setGetRegex($regex);

    /**
     * @return array
     */
    function getQueryRegex();

    /**
     * @param $regex
     */
    function setQueryRegex($regex);

    /**
     * @return array
     */
    function getPostRegex();

    /**
     * @param $regex
     */
    function setPostRegex($regex);

    /**
     * Takes login/submit/session/{sid}/redirect/{location}?and=something
     * and returns @^login/submit/session/([a-z0-9]+)/redirect/([a-zA-Z0-9|%]+)$@
     * according to router.ini
     *
     * @param $pattern
     * @param array $data
     * @return string
     */
    function replaceCurlyParametersWithRegex($pattern, array $data);

    /**
     * Removes everything after ? (eg. ?param1=1&param2=2)
     *
     * @param $str
     * @param string $marker
     * @return mixed
     */
    function getCleanUrl($str, $marker='?');

    /**
     * @param $get_data
     * @return mixed
     */
    function filterQueryKeys($get_data);

    /**
     * @param $get_data
     * @return mixed
     */
    function filterGetKeys($get_data);

    /**
     * @param $request_path
     * @return bool
     */
    function matchesByPath($request_path);

    /**
     * @param string $method
     */
    function setMethod($method);

    function getMethod();

    /**
     * @param boolean $secure
     */
    function setIsSecure($secure);

    /**
     * @return boolean
     */
    function isSecure();

    /**
     * @param $allowed_tags
     */
    function setAllowedTags($allowed_tags);

    /**
     * @return string|null
     */
    function getAllowedTags();
}