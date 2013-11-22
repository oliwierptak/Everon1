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

use Everon\Interfaces;
use Everon\Exception;

interface View
{
    /**
     * @param $name
     */
    function setName($name);

    function getName();
        
    /**
     * @return Interfaces\TemplateContainer
     */
    function getContainer();

    /**
     * @param mixed $Container Instance of Interfaces\TemplateContainer, string or array
     * @throws Exception\Template
     */
    function setContainer($Container);
    
    function getTemplateDirectory();

    /**
     * @param $directory
     */
    function setTemplateDirectory($directory);

    /**
     * @param $name
     * @return \SplFileInfo
     */    
    function getTemplateFilename($name);

    /**
     * @param $name
     * @param $data
     * @return Interfaces\TemplateContainer
     */    
    function getTemplate($name, $data);

    /**
     * @param $name
     * @param mixed $value
     */    
    function set($name, $value);

    /**
     * @param $name
     * @param mixed|null $default
     * @return null
     */
    function get($name, $default=null);

    /**
     * @param $name
     */    
    function delete($name);

    /**
     * @return array
     */    
    function getData();

    /**
     * @param array $data
     */    
    function setData(array $data);
    
    /**
     * @param $action
     * @return Interfaces\Template Complete page with 'header, body and footer'
     */
    function getViewTemplateByAction($action);
    
    function url($url);

    /**
     * @param $extension
     */    
    function setDefaultExtension($extension);
    
    function getDefaultExtension();

    /**
     * @return Interfaces\Template Loads 'index.htm' from template directory
     */    
    function getViewTemplate();
}