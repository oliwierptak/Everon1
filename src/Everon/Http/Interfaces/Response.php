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
    function setContentType($content_type);
    function getCharset();
    function setCharset($charset);
    function setHeader($name, $value);
    function getHeader($name);
    function getHeaderCollection();
    function setHeaderCollection(HeaderCollection $Collection);
    function setStatusCode($status);
    function getStatusCode();
    function getStatusMessage();
    function setStatusMessage($message);
    function send();
    function toHtml();
}