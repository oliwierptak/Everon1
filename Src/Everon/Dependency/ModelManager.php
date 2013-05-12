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


trait ModelManager
{

    protected $ModelManager = null;


    /**
     * @return \Everon\Interfaces\ModelManager
     */
    public function getModelManager()
    {
        return $this->ModelManager;
    }

    /**
     * @param \Everon\Interfaces\ModelManager $Manager
     */
    public function setModelManager(\Everon\Interfaces\ModelManager $Manager)
    {
        $this->ModelManager = $Manager;
    }

}
