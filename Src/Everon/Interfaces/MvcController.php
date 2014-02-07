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

interface MvcController extends Interfaces\Controller
{

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
     * Takes default view and sets exception message as body
     *
     * @param \Exception $Exception
     * @param int $code Http status code. Default is 400.
     */
    function showException(\Exception $Exception, $code=400);
}
