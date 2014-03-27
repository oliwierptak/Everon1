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

trait ConfigLoader
{

    /**
     * @var \Everon\Config\Interfaces\Loader
     */
    protected $ConfigLoader = null;


    /**
     * @return \Everon\Config\Interfaces\Loader
     */
    public function getConfigLoader()
    {
        return $this->ConfigLoader;
    }

    /**
     * @param \Everon\Config\Interfaces\Loader $ConfigLoader
     */
    public function setConfigLoader(\Everon\Config\Interfaces\Loader $ConfigLoader)
    {
        $this->ConfigLoader = $ConfigLoader;
    }

}
