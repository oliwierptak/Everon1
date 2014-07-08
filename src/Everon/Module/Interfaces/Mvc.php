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
use Everon\Interfaces\Module;
use Everon\View;

interface Mvc extends Module, View\Interfaces\Dependency\Manager
{
    /**
     * @param $name
     * @return View\Interfaces\View
     */
    function getViewByName($name);

    /**
     * @param View\Interfaces\View $View
     */
    function setViewByViewName(View\Interfaces\View $View);
}
