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

use Everon\Exception;

interface View
{        
    function getName();

    /**
     * @return Template
     * @throws \Everon\Exception\View
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
     * @param $data
     * @return Template
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
     * @param $extension
     */    
    function setDefaultExtension($extension);
    
    function getDefaultExtension();

    /**
     * @return \SplFileInfo
     */
    function getFilename();


    /**
     * @param $action
     * @return mixed
     */
    function execute($action);

    /**
     * @param $name
     * @param array $query
     * @param array $get
     * @return string
     * @throws \Everon\Exception\Controller
     */
    function getUrl($name, $query=[], $get=[]);

    /**
     * @param array $data
     * @return \Everon\Helper\PopoProps
     */
    function templetize(array $data);


    /**
     * @param array $data Array of items implementing Arrayable Interface
     * @return array Array of Helper\PopoProps objects
     */
    function templetizeCollection(array $data);
}