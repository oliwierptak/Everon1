<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Interfaces;

use Everon\Interfaces;

interface Controller extends Interfaces\Controller
{
    function addResourceFromRequest();

    function serveResourceFromRequest();

    function deleteResourceFromRequest();
    
    /**
     * @return mixed
     */
    function getModel();

    /**
     * @return Interfaces\Resource
     */
    function getResourceFromRequest();

}
