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

interface RelationMapper
{
    /**
     * @param string $column
     */
    function setColumn($column);

    /**
     * @return string
     */
    function getColumn();

    /**
     * @param string $inversed_by
     */
    function setInversedBy($inversed_by);

    /**
     * @return string
     */
    function getInversedBy();

    /**
     * @param string $mapped_by
     */
    function setMappedBy($mapped_by);

    /**
     * @return string
     */
    function getMappedBy();

    /**
     * @param string $target_entity
     */
    function setTargetEntity($target_entity);

    /**
     * @return string
     */
    function getTargetEntity();
}