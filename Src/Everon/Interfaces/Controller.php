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

interface Controller
{
    function getName();
    function setName($name);

    /**
     * @param $name
     * @return Interfaces\View
     */
    public function getView($name=null);

    /**
     * @return Interfaces\ViewManager $Manager
     */
    function getViewManager();

    /**
     * @param Interfaces\ViewManager $Manager
     */
    function setViewManager(Interfaces\ViewManager $Manager);

    /**
     * @return Interfaces\ModelManager
     */
    function getModelManager();

    /**
     * @param Interfaces\ModelManager $Manager
     */
    public function setModelManager(Interfaces\ModelManager $Manager);

    /**
     * @return Interfaces\Request
     */
    public function getRequest();

    /**
     * @param Interfaces\Request $Request
     * @return void
     */
    function setRequest(Interfaces\Request $Request);

    /**
     * @return Interfaces\Router
     */
    function getRouter();

    /**
     * @param Interfaces\Router $Router
     * @return void
     */
    function setRouter(Interfaces\Router $Router);

    /**
     * @param $name
     * @return mixed
     */
    function getModel($name=null);

    /**
     * @param $result
     * @param Interfaces\Response $Response
     * @return Interfaces\Response
     */
    function result($result, Interfaces\Response $Response);

}
