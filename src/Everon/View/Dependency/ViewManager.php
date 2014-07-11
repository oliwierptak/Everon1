<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Dependency;


trait ViewManager
{

    /**
     * @var \Everon\View\Interfaces\View
     */
    protected $ViewManager = null;


    /**
     * @return \Everon\View\Interfaces\Manager
     */
    public function getViewManager()
    {
        return $this->ViewManager;
    }

    /**
     * @param \Everon\View\Interfaces\Manager $Manager
     */
    public function setViewManager(\Everon\View\Interfaces\Manager $Manager)
    {
        $this->ViewManager = $Manager;
    }

}
