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

use Everon\Interfaces;

interface Response extends Interfaces\Response 
{
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