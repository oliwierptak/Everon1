<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 28/07/14
 * Time: 09:22
 */
namespace Everon\Module\Interfaces;

use Everon\Interfaces;

interface Module
{
    /**
     * @param $directory
     */
    function setDirectory($directory);

    /**
     * @param Interfaces\FactoryWorker $FactoryWorker
     */
    function setFactoryWorker(Interfaces\FactoryWorker $FactoryWorker);

    /**
     * @return null
     */
    function getName();

    /**
     * @return Interfaces\FactoryWorker
     */
    function getFactoryWorker();

    function setup();

    /**
     * @param $name
     */
    function setName($name);

    /**
     * @param $name
     * @return null
     */
    function getController($name);

    /**
     * @return null
     */
    function getDirectory();
}