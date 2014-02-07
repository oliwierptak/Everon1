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

    protected $ConfigLoader = null;


    /**
     * @return \Everon\Interfaces\ConfigLoader
     */
    public function getConfigLoader()
    {
        return $this->ConfigLoader;
    }

    /**
     * @param \Everon\Interfaces\ConfigLoader $ConfigLoader
     */
    public function setConfigLoader(\Everon\Interfaces\ConfigLoader $ConfigLoader)
    {
        $this->ConfigLoader = $ConfigLoader;
    }

}
