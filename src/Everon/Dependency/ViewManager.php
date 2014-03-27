<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency;


trait ViewManager
{

    /**
     * @var \Everon\Interfaces\View
     */
    protected $ViewManager = null;


    /**
     * @return \Everon\Interfaces\ViewManager
     */
    public function getViewManager()
    {
        return $this->ViewManager;
    }

    /**
     * @param \Everon\Interfaces\ViewManager $Manager
     */
    public function setViewManager(\Everon\Interfaces\ViewManager $Manager)
    {
        $this->ViewManager = $Manager;
    }

}
