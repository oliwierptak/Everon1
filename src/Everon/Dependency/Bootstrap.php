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


trait Bootstrap
{
    /**
     * @var \Everon\Interfaces\Bootstrap
     */
    protected $Bootstrap = null;


    /**
     * @return \Everon\Interfaces\Bootstrap
     */
    public function getBootstrap()
    {
        return $this->Bootstrap;
    }

    public function setBootstrap(\Everon\Interfaces\Bootstrap $Bootstrap)
    {
        $this->Bootstrap = $Bootstrap;
    }

}