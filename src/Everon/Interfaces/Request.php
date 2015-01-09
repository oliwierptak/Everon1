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

    /**
     * @return string
     */
    function getLocation();

    /**
     * @param $method
     */
    function setMethod($method);

    /**
     * @return string
     */
    function getMethod();

    /**
     * @param $url
     */
    function setUrl($url);

    /**
     * @return string
     */
    function getUrl();

    /**
     * @param $path
     */
    function setPath($path);

    /**
     * @return string
     */
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

    /**
     * @return int
     */
    function getPort();

    /**
     * @param $protocol
     */
    function setProtocol($protocol);

    /**
     * @return string
     */
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
     * @param null $allowed_tags
     */
    function setPostCollection(array $data, $allowed_tags = null);

    /**
     * @return Collection
     */
    function getPostCollection();

    /**
     * @return bool
     */
    function isEmptyUrl();

    /**
     * @param string $default
     * @return string
     */
    function getPreferredLanguageCode($default='en-US');

    /**
     * @return array
     */
    public function getRawInput();

    /**
     * @param $name
     * @param $default
     * @return mixed
     */
    function getHeader($name, $default);

    /**
     * @return mixed
     */
    function getIpAddress();

    /**
     * @return string
     */
    function getUserAgent();

    /**
     * @return resource
     */
    function getPhpInputContext();

    /**
     * @param \resource $php_input_context
     */
    function setPhpInputContext($php_input_context);

    /**
     * @return boolean
     */
    function getPhpInputFlags();

    /**
     * @param boolean $php_input_flags
     */
    function setPhpInputFlags($php_input_flags);

    /**
     * @param boolean $is_ajax
     */
    function setIsAjax($is_ajax);

    /**
     * @return boolean
     */
    function isAjax();

    /**
     * @param $input
     * @param $allowed_tags
     * @return mixed
     */
    function sanitizeInput($input, $allowed_tags=null);
}