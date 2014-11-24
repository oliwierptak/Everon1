<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 28/07/14
 * Time: 09:22
 */
namespace Everon\Module\Interfaces;

use Everon\Interfaces\FactoryWorker;

interface Module extends 
    Dependency\ModuleManager,
    \Everon\Config\Interfaces\Dependency\Config,
    \Everon\Interfaces\Dependency\Factory
{
    /**
     * @param $directory
     */
    function setDirectory($directory);

    /**
     * @return string
     */
    function getDirectory();

    /**
     * @param FactoryWorker $FactoryWorker
     */
    function setFactoryWorker(FactoryWorker $FactoryWorker);

    /**
     * @return FactoryWorker
     */
    function getFactoryWorker();

    /**
     * @return string
     */
    function getName();

    /**
     * @param $name
     */
    function setName($name);

    function setup();

    /**
     * @param $name
     * @return \Everon\Interfaces\Controller
     */
    function getController($name);

    /**
     * @param $name
     * @return \Everon\Ajax\Interfaces\Controller
     */
    function getAjaxController($name);
}