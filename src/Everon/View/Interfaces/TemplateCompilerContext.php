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

interface TemplateCompilerContext
{
    function setScopeName($name);
    function getScopeName();
    function setCompiled($compiled);
    function getCompiled();
    function setPhp($php);
    function getPhp();
    function getData();
    function setData(array $data);
    
    /**
     * @param mixed $Scope
     */
    function setScope($Scope);

    /**
     * @return mixed
     */
    function getScope();
}