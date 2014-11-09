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

trait ConfigCacheLoader
{

    /**
     * @var \Everon\Config\Interfaces\LoaderCache
     */
    protected $ConfigCacheLoader = null;


    /**
     * @return \Everon\Config\Interfaces\LoaderCache
     */
    public function getConfigCacheLoader()
    {
        return $this->ConfigCacheLoader;
    }

    /**
     * @param \Everon\Config\Interfaces\LoaderCache $ConfigCacheLoader
     */
    public function setConfigCacheLoader(\Everon\Config\Interfaces\LoaderCache $ConfigCacheLoader)
    {
        $this->ConfigCacheLoader = $ConfigCacheLoader;
    }

}
