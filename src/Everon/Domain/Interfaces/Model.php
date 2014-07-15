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

interface Model
{
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
     * @param Entity $Entity
     * @param int $user_id
     * @return Entity
     */
    function beforeAdd(Entity $Entity, $user_id);

    /**
     * @param Entity $Entity
     * @param int $user_id
     * @return Entity
     */
    function beforeSave(Entity $Entity, $user_id);

    /**
     * @param Entity $Entity
     * @param int $user_id
     * @return Entity
     */
    function beforeDelete(Entity $Entity, $user_id);

    /**
     * @param array $data
     * @param int $user_id
     * @return Entity
     */
    function add(array $data, $user_id=null);

    /**
     * @param array $data
     * @param int $user_id
     * @return mixed
     */
    function save(array $data, $user_id=null);

    /**
     * @param $id
     * @param int $user_id
     * @return mixed
     */
    function delete($id, $user_id=null);

    /**
     * @param array $data
     */
    function validateEntityData(array $data);
}
