<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Dependency;

trait ResourceManager
{
    /**
     * @var \Everon\Rest\Interfaces\ResourceManager
     */
    protected $ResourceManager = null;


    /**
     * @return \Everon\Rest\Interfaces\ResourceManager
     */
    public function getResourceManager()
    {
        return $this->ResourceManager;
    }

    /**
     * @param \Everon\Rest\Interfaces\ResourceManager $ResourceManager
     */
    public function setResourceManager(\Everon\Rest\Interfaces\ResourceManager $ResourceManager)
    {
        $this->ResourceManager = $ResourceManager;
    }

}
