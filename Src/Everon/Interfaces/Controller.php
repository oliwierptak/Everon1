<?php
namespace Everon\Interfaces;

interface Controller
{
    function getModel($name);
    function getAllModels();
    function setOutput($Output);
    function getOutput();
    function getName();
    function setName($name);

    /**
     * @return View
     */
    function getView();

    /**
     * @param View $View
     */
    function setView(View $View);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Request $Request
     * @return void
     */
    function setRequest(Request $Request);

    /**
     * @return Router
     */
    function getRouter();

    /**
     * @param Router $Router
     * @return void
     */
    function setRouter(Router $Router);

    /**
     * @return Factory
     */
    function getFactory();

    /**
     * @param Factory $Factory
     * @return void
     */
    function setFactory(Factory $Factory);

    /**
     * @return \Everon\Interfaces\Response
     */
    function getResponse();

    /**
     * @param \Everon\Interfaces\Response $Response
     */
    function setResponse(\Everon\Interfaces\Response $Response);    
}
