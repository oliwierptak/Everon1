<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Task\Dependency;


trait TaskManager
{
    /**
     * @var \Everon\Task\Interfaces\Manager
     */
    protected $TaskManager = null;


    /**
     * @return \Everon\Task\Interfaces\Manager
     */
    public function getTaskManager()
    {
        return $this->TaskManager;
    }

    /**
     * @param \Everon\Task\Interfaces\Manager
     */
    public function setTaskManager(\Everon\Task\Interfaces\Manager $Manager)
    {
        $this->TaskManager = $Manager;
    }

}
