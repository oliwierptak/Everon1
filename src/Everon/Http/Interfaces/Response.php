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

interface Response extends \Everon\Interfaces\Response 
{
    /**
     * @param \Everon\Http\Interfaces\CookieCollection $CookieCollection
     */
    function setCookieCollection($CookieCollection);

    /**
     * @return \Everon\Http\Interfaces\CookieCollection
     */
    function getCookieCollection();
        
    /**
     * @param Cookie $Cookie
     */
    function addCookie(Cookie $Cookie);

    /**
     * @param Cookie $Cookie
     */
    function deleteCookie(Cookie $Cookie);

    /**
     * @param Cookie $name
     */
    public function deleteCookieByName($name);

    /**
     * @param $name
     * @return Cookie|null
     */
    function getCookie($name);
    
    function getContentType();

    /**
     * @param $content_type
     */
    function setContentType($content_type);
    
    function getCharset();

    /**
     * @param $charset
     */
    function setCharset($charset);

    /**
     * @param $name
     * @param $value
     */
    function setHeader($name, $value);

    /**
     * @param $name
     */
    function getHeader($name);
    
    /**
     * @return HeaderCollection
     */
    function getHeaderCollection();

    /**
     * @param HeaderCollection $Collection
     */
    function setHeaderCollection(HeaderCollection $Collection);

    /**
     * @param $status
     */
    function setStatusCode($status);
    function getStatusCode();
    function getStatusMessage();

    /**
     * @param string $status_message
     */
    function setStatusMessage($status_message);
    
    function send();
    
    function toHtml();
}