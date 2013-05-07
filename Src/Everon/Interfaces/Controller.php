<?php
namespace Everon\Interfaces;

use Everon\Interfaces;

interface Controller
{
    function setOutput($Output);
    function getOutput();
    function getName();
    function setName($name);

    /**
     * @return Interfaces\View
     */
    function getView();

    /**
     * @param Interfaces\View $View
     */
    function setView(Interfaces\View $View);

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
    function getModel($name);

    /**
     * @param Interfaces\Response $Response
     * @return mixed
     */
    function result(Interfaces\Response $Response);

}
