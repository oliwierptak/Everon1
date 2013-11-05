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


interface ConfigItem
{
    function getName();
    function setName($name);
    function isDefault();
    function setIsDefault($is_default);
    function toArray();
}