<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;

interface CurlAdapter
{
    /**
     * @param $url
     * @return mixed|null
     */
    function head($url);

    /**
     * @param $url
     * @return mixed|null
     */
    function delete($url);

    /**
     * @return int
     */
    function getHttpResponseCode();

    /**
     * @param int $http_response_code
     */
    function setHttpResponseCode($http_response_code);

    /**
     * @param $url
     * @return mixed|null
     */
    function get($url);

    /**
     * @param $url
     * @param $data
     * @return mixed|null
     */
    function post($url, $data);

    /**
     * @param $url
     * @param $data
     * @return mixed|null
     */
    function put($url, $data);
}