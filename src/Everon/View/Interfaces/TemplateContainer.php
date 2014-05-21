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

interface TemplateContainer extends \Everon\Interfaces\Arrayable
{
    /**
     * @param $name
     * @param $value
     * @return $this
     */
    function set($name, $value);

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    function get($name, $default=null);

    /**
     * @param $name
     */
    function delete($name);

    /**
     * @param string $content
     */
    function setCompiledContent($content);

    /**
     * @return string
     */
    function getCompiledContent();

    /**
     * @return string
     */
    function getTemplateContent();

    /**
     * @param string $content
     */
    function setTemplateContent($content);

    /**
     * @param TemplateCompilerScope $Scope
     */
    function setScope(TemplateCompilerScope $Scope);
    
    /**
     * @return TemplateCompilerScope
     */
    function getScope();

    /**
     * @return array
     */
    function getData();

    /**
     * @param array $data
     */
    function setData(array $data);    
}