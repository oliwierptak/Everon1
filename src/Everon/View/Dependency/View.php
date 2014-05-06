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


trait View
{

    /**
     * @var \Everon\View\Interfaces\View
     */
    protected $View = null;


    /**
     * @return \Everon\View\Interfaces\View
     */
    public function getView()
    {
        return $this->View;
    }

    /**
     * @param \Everon\View\Interfaces\View $View
     */
    public function setView(\Everon\View\Interfaces\View $View)
    {
        $this->View = $View;
    }

}
