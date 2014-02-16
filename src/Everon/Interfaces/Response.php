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
    function setData($data);
    function getData();
    function setResult($result);
    function getResult();
    function toJson();
    function toText();
}