<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Module\Interfaces;

use Everon\Interfaces\Dependency;
use Everon\View;

interface Mvc extends Module, View\Interfaces\Dependency\Manager
{
    /**
     * @param $layout_name
     * @param $view_name
     * @internal param $name
     * @return View\Interfaces\View
     */
    function getViewByName($layout_name, $view_name);

    /**
     * @param $layout_name
     * @param View\Interfaces\View $View
     * @return void
     */
    function setViewByViewName($layout_name, View\Interfaces\View $View);
}
