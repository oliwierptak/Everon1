<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event\Dependency;

trait Dispatcher
{
    /**
     * @var \Everon\Event\Interfaces\Dispatcher
     */
    protected $Dispatcher = null;

    /**
     * @param \Everon\Event\Interfaces\Dispatcher $Dispatcher
     */
    public function setDispatcher($Dispatcher)
    {
        $this->Dispatcher = $Dispatcher;
    }

    /**
     * @return \Everon\Event\Interfaces\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->Dispatcher;
    }


}
