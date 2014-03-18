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
    public function head($url);

    /**
     * @param $url
     * @return mixed|null
     */
    public function delete($url);

    /**
     * @return int
     */
    public function getHttpResponseCode();

    /**
     * @param int $http_response_code
     */
    public function setHttpResponseCode($http_response_code);

    /**
     * @param $url
     * @return mixed|null
     */
    public function get($url);

    /**
     * @param $url
     * @param $data
     * @return mixed|null
     */
    public function post($url, $data);

    /**
     * @param $url
     * @param $data
     * @return mixed|null
     */
    public function put($url, $data);
}