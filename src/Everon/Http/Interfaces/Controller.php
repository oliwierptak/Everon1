<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http\Interfaces;

use Everon\View\Interfaces;

interface Controller extends \Everon\Interfaces\Controller
{
    /**
     * @param $flash_message
     */
    function setFlashMessage($flash_message);

    function getFlashMessage();

    function resetFlashMessage();
}
