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

interface Response
{
    function getContentType();
    function setContentType($content_type);
    function getCharset();
    function setCharset($charset);
    function toJson();
    function toText();
    function setData($data);
    function getData();
    function setResult($result);
    function getResult();
    function setStatus($status);
    function getStatus();
    function getStatusMessage();
    function setStatusMessage($message);
}