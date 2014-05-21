<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Interfaces;

interface TemplateCompilerScope
{
    function setName($name);
    function getName();
    function setCompiled($compiled);
    function getCompiled();
    function setPhp($php);
    function getPhp();
    function getData();
    function setData(array $data);    
}