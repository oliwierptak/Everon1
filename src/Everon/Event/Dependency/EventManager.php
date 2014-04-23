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
    protected $Dispatcher = null;

    /**
     * @param \Everon\Event\Interfaces\Manager $Dispatcher
     */
    public function setEventManager($Dispatcher)
    {
        $this->Dispatcher = $Dispatcher;
    }

    /**
     * @return \Everon\Event\Interfaces\Manager
     */
    public function getEventManager()
    {
        return $this->Dispatcher;
    }


}
