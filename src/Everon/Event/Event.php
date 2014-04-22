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

use Everon\Event\Interfaces\Dispatcher;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class Event implements Interfaces\Event
{

    /**
     * @var Dispatcher $Dispatcher
     */
    private $Dispatcher;

    private $name;

    /**
     * @param mixed $Dispatcher
     */
    public function setDispatcher(Dispatcher $Dispatcher)
    {
        $this->Dispatcher = $Dispatcher;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->Dispatcher;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


} 