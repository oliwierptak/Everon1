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

interface Request
{
    function setMethod($method);
    function getMethod();
    function setUrl($url);
    function getUrl();
    function setLocation($location);
    function getLocation();
    function getGetParameter($name, $default=null);
    function setGetParameter($name, $value);
    function getQueryParameter($name, $default=null);
    function setQueryParameter($name, $value);
    function getPostParameter($name, $default=null);
    function setPostParameter($name, $value);
    function setFileCollection(array $files);
    function getFileCollection();
    function isSecure();
    function getQueryString();
    function setQueryString($query_string);
    function setPort($port);
    function getPort();
    function setProtocol($protocol);
    function getProtocol();
    function setServerCollection(array $data);
    function getServerCollection();
    function setGetCollection(array $data);
    function getGetCollection();
    function setQueryCollection(array $data);
    function getQueryCollection();
    function setPostCollection(array $data);
    function getPostCollection();
    function isEmptyUrl();   
}