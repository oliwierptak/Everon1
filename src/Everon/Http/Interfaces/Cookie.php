<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http\Interfaces;

interface Cookie
{
    function delete();

    /**
     * @param string $date_value
     * @return void
     */
    function setExpireDateFromString($date_value);

    function neverExpire();

    /**
     * @return string
     */
    function getValue();

    /**
     * @return string
     */
    function getValueAsJson();

    /**
     * @param $json
     */
    function setDataFromJson($json);

    /**
     * @return string
     */
    function getName();

    /**
     * @param string $path
     */
    function setPath($path);

    function isExpired();

    /**
     * @param int $expire_date
     */
    function setExpire($expire_date);

    /**
     * @param string $domain
     */
    function setDomain($domain);

    /**
     * @param string $value
     */
    function setValue($value);

    /**
     * @return int
     */
    function getExpire();

    /**
     * @return string
     */
    function getPath();

    /**
     * @param string $name
     */
    function setName($name);

    /**
     * @return string
     */
    function getDomain();

    function enableHttpOnly();

    function disableHttpOnly();

    /**
     * @return boolean
     */
    function isHttpOnly();

    function enableJson();

    function disableJson();
    
    /**
     * @return boolean
     */
    function isJsonEnabled();

    function enableSecure();

    function disableSecure();

    /**
     * @return boolean
     */
    function isSecure();

    /**
     * @param boolean $existing
     */
    function setIsExisting($existing);

    /**
     * @return boolean
     */
    function isExisting();
}