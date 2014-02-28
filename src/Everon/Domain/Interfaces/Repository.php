<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Interfaces;

use Everon\Interfaces\DataMapper;
use Everon\Domain\Interfaces\Entity;
use Everon\Domain\Exception;

interface Repository
{
    /**
     * @param $id
     * @return Entity
     * @throws Exception\Repository
     */
    function getEntityById($id);
        
    /**
     * @param Entity $Entity
     */
    function persist(Entity $Entity);

    /**
     * @param Entity $Entity
     */
    function remove(Entity $Entity);

    /**
     * @return DataMapper
     */    
    function getMapper();

    /**
     * @param DataMapper $Mapper
     */    
    function setMapper(DataMapper $Mapper);

    function getName();
}
