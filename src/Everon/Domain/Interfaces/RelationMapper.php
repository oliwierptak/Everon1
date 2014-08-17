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
    function setDomainName($target_entity);

    /**
     * @return string
     */
    function getDomainName();

    /**
     * @param string $type
     */
    function setType($type);

    /**
     * @return string
     */
    function getType();

    /**
     * @param boolean $is_virtual
     */
    function setIsVirtual($is_virtual);

    /**
     * @return boolean
     */
    function isVirtual();

    /**
     * @param boolean $is_owning_side
     */
    function setIsOwningSide($is_owning_side);

    /**
     * @return boolean
     */
    function isOwningSide();
}