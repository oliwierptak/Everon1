<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Mvc\Interfaces;

use Everon\View\Interfaces;

interface Controller extends \Everon\Interfaces\Controller, \Everon\View\Interfaces\Dependency\Manager
{
    /**
     * @return Interfaces\TemplateContainer
     */
    function getActionTemplate();

    /**
     * @param Interfaces\TemplateContainer $Template
     */
    function setActionTemplate(Interfaces\TemplateContainer $Template);
        
    /**
     * @return Interfaces\View
     */
    function getView();

    /**
     * @param Interfaces\View $View
     */
    function setView(Interfaces\View $View);

    /**
     * @param string $view_name
     */
    function setViewName($view_name);

    /**
     * @return string
     */
    function getViewName();

    /**
     * @param string $layout_name
     */
    function setLayoutName($layout_name);

    /**
     * @return string
     */
    function getLayoutName();
    
    /**
     * @param \Exception $Exception
     */
    function showException(\Exception $Exception);

    /**
     * @param null $errors
     */
    function showValidationErrors($errors=null);

    /**
     * @param $msg
     * @param array $parameters
     */
    function error($msg, array $parameters=[]);

    /**
     * @param $name
     * @param array $query
     * @param array $get
     */
    function redirect($name, $query=[], $get=[]);
}
