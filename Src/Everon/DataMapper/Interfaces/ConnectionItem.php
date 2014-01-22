<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

interface ConnectionItem extends \Everon\Interfaces\Immutable, \Everon\Interfaces\Arrayable
{
    function getDsn();
    function getDriver();
    function getHost();
    function getName();
    function getEncoding();
    function getUsername();
    function getPassword();
    function getOptions();
    function toPdo();
}
