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

interface Core extends \Everon\Interfaces\Core
{
    /**
     * @param $name
     * @param array $query
     * @param array $get
     */
    function redirect($name, $query=[], $get=[]);
}
