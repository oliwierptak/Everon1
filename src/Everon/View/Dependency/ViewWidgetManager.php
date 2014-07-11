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


trait ViewWidgetManager
{

    /**
     * @var \Everon\View\Interfaces\WidgetManager
     */
    protected $ViewWidgetManager = null;


    /**
     * @return \Everon\View\Interfaces\WidgetManager
     */
    public function getViewWidgetManager()
    {
        return $this->ViewWidgetManager;
    }

    /**
     * @param \Everon\View\Interfaces\WidgetManager $Manager
     */
    public function setViewWidgetManager(\Everon\View\Interfaces\WidgetManager $Manager)
    {
        $this->ViewWidgetManager = $Manager;
    }

}
