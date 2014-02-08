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


trait Router
{

    protected $Router = null;


    /**
     * @return \Everon\Interfaces\Router
     */
    public function getRouter()
    {
        return $this->Router;
    }

    /**
     * @param \Everon\Interfaces\Router $Router
     */
    public function setRouter(\Everon\Interfaces\Router $Router)
    {
        $this->Router = $Router;
    }

}
