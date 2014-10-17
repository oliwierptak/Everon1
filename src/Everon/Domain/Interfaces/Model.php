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

interface Model extends \Everon\Domain\Interfaces\Dependency\DomainManager
{
    /**
     * @param $id
     * @return Entity|null
     */
    function getById($id);
    
    /**
     * @param Entity $Entity
     * @param $relation_name
     * @param array $data
     * @param null $user_id
     */
    function addCollection(Entity $Entity, $relation_name, array $data, $user_id=null);

    /**
     * @param Entity $Entity
     * @param $relation_name
     * @param array $data
     * @param null $user_id
     */
    function saveCollection(Entity $Entity, $relation_name, array $data, $user_id=null);

    /**
     * Send an array with ids of items to delete, eg [{id: 2}, {id: 3}]
     * 
     * @param Entity $Entity
     * @param $relation_name
     * @param array $data
     * @param null $user_id
     */
    function deleteCollection(Entity $Entity, $relation_name, array $data, $user_id=null);
        
    /**
     * @param array $data
     * @return Entity
     */
    function create(array $data=[]);
        
    /**
     * @param string $name
     */
    function setName($name);
        
    /**
     * @return string
     */
    function getName();

    /**
     * @return Repository
     */
    function getRepository();

    /**
     * @param Repository $Repository
     */
    function setRepository(Repository $Repository);
}
