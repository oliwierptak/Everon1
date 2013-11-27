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
}
