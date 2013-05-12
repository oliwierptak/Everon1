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


interface TemplateContainer
{
    function set($name, $value);
    function get($name, $default=null);
    function setCompiledContent($data);
    function getCompiledContent();
    function getTemplateContent();
    function setTemplateContent($content);
    function toArray();
    function getData();
    function setAllIncludes(array $includes);
    function getAllIncludes();
}