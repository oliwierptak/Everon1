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
     * @var \Everon\Interfaces\View
     */
    protected $View = null;


    /**
     * @return \Everon\Interfaces\View
     */
    public function getView()
    {
        return $this->View;
    }

    /**
     * @param \Everon\Interfaces\View $View
     */
    public function setView(\Everon\Interfaces\View $View)
    {
        $this->View = $View;
    }

}
