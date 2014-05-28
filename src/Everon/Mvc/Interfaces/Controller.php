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
     * @return Interfaces\View
     */
    function getView();

    /**
     * @return mixed
     */
    function getModel();

    /**
     * @param \Exception $Exception
     */
    function showException(\Exception $Exception);

    /**
     * @param $name
     * @param array $query
     * @param array $get
     */
    function redirect($name, $query=[], $get=[]);
}
