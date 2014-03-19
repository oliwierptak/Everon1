<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Dependency;


trait DataMapperManager
{
    /**
     * @var \Everon\DataMapper\Interfaces\Manager
     */
    protected $DataMapperManager = null;


    /**
     * @return \Everon\DataMapper\Interfaces\Manager
     */
    public function getDataMapperManager()
    {
        return $this->DataMapperManager;
    }

    /**
     * @param \Everon\DataMapper\Interfaces\Manager
     */
    public function setDataMapperManager(\Everon\DataMapper\Interfaces\Manager $Manager)
    {
        $this->DataMapperManager = $Manager;
    }

}
