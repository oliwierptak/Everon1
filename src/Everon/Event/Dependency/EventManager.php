<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event\Dependency;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
trait EventManager
{
    /**
     * @var \Everon\Event\Interfaces\Manager
     */
    protected $EventManager = null;

    /**
     * @param \Everon\Event\Interfaces\Manager $EventManager
     */
    public function setEventManager(\Everon\Event\Interfaces\Manager $EventManager)
    {
        $this->EventManager = $EventManager;
    }

    /**
     * @return \Everon\Event\Interfaces\Manager
     */
    public function getEventManager()
    {
        return $this->EventManager;
    }
}