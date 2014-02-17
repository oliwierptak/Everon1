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
use Everon\Interfaces\View;

interface Mvc extends Module, Dependency\ViewManager
{
    /**
     * @param $name
     * @return View
     */
    function getView($name);
}
