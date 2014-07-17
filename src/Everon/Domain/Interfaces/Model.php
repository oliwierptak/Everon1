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
     * @return Entity
     */
    function create();
        
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
     * @param array $data
     */
    function validateEntityData(array $data);
}
