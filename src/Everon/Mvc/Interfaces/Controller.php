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

use Everon\Interfaces;

interface Controller extends Interfaces\Controller
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
     * @return Interfaces\ViewManager
     */
    function getViewManager();

    /**
     * @param \Exception $Exception
     * @param int $code
     */
    function showException(\Exception $Exception, $code=400);
}
