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
    Dependency\Manager,
    \Everon\Config\Interfaces\Dependency\Config,
    \Everon\Interfaces\Dependency\Factory
{
    /**
     * @param $directory
     */
    function setDirectory($directory);

    /**
     * @param FactoryWorker $FactoryWorker
     */
    function setFactoryWorker(FactoryWorker $FactoryWorker);

    /**
     * @return string
     */
    function getName();

    /**
     * @return FactoryWorker
     */
    function getFactoryWorker();

    function setup();

    /**
     * @param $name
     */
    function setName($name);

    /**
     * @param $name
     * @return string
     */
    function getController($name);

    /**
     * @return string
     */
    function getDirectory();
}