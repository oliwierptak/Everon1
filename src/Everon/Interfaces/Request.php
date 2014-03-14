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

interface Request extends Arrayable
{
    /**
     * @param $location
     */
    function setLocation($location);
    function getLocation();

    /**
     * @param $method
     */
    function setMethod($method);
    function getMethod();

    /**
     * @param $url
     */
    function setUrl($url);
    function getUrl();

    /**
     * @param $path
     */
    function setPath($path);
    function getPath();

    /**
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    function getGetParameter($name, $default=null);

    /**
     * @param $name
     * @param $value
     */
    function setGetParameter($name, $value);

    /**
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    function getQueryParameter($name, $default=null);

    /**
     * @param $name
     * @param $value
     */
    function setQueryParameter($name, $value);

    /**
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    function getPostParameter($name, $default=null);

    /**
     * @param $name
     * @param $value
     */
    function setPostParameter($name, $value);

    /**
     * @param array $files
     */
    function setFileCollection(array $files);

    /**
     * @return Collection
     */
    function getFileCollection();

    /**
     * @return bool
     */
    function isSecure();
    function getQueryString();

    /**
     * @param $query_string
     */
    function setQueryString($query_string);

    /**
     * @param $port
     */
    function setPort($port);
    
    function getPort();

    /**
     * @param $protocol
     */
    function setProtocol($protocol);
    
    function getProtocol();

    /**
     * @param array $data
     */
    function setServerCollection(array $data);

    /**
     * @return Collection
     */
    function getServerCollection();

    /**
     * @param array $data
     */
    function setGetCollection(array $data);

    /**
     * @return Collection
     */
    function getGetCollection();

    /**
     * @param array $data
     */
    function setQueryCollection(array $data);

    /**
     * @return Collection
     */
    function getQueryCollection();

    /**
     * @param array $data
     */
    function setPostCollection(array $data);

    /**
     * @return Collection
     */
    function getPostCollection();

    /**
     * @return bool
     */
    function isEmptyUrl();

    /**
     * http://stackoverflow.com/questions/6038236/http-accept-language
     * @return array
     */
    function getPreferredLanguageCode();
}