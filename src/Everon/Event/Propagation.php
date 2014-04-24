<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class Propagation extends \SplEnum 
{
    const RUNNING = 1;
    const HALTED = 2;
    const __default = self::RUNNING;
}