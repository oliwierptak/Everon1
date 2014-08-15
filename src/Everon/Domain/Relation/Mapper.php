<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Relation;

use Everon\Domain\Interfaces;
use Everon\Helper;

class Mapper implements Interfaces\RelationMapper
{
    /**
     * @var string
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $target_entity = null;

    /**
     * @var string
     */
    protected $mapped_by = null;

    /**
     * @var string
     */
    protected $inversed_by = null;
    
    
    public function __construct($target_entity, $column, $mapped_by=null, $inversed_by=null)
    {
        $this->target_entity = $target_entity;
        $this->column = $column;
        $this->mapped_by = $mapped_by;
        $this->inversed_by = null;
    }

    /**
     * @param string $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param string $inversed_by
     */
    public function setInversedBy($inversed_by)
    {
        $this->inversed_by = $inversed_by;
    }

    /**
     * @return string
     */
    public function getInversedBy()
    {
        return $this->inversed_by;
    }

    /**
     * @param string $mapped_by
     */
    public function setMappedBy($mapped_by)
    {
        $this->mapped_by = $mapped_by;
    }

    /**
     * @return string
     */
    public function getMappedBy()
    {
        return $this->mapped_by;
    }

    /**
     * @param string $target_entity
     */
    public function setTargetEntity($target_entity)
    {
        $this->target_entity = $target_entity;
    }

    /**
     * @return string
     */
    public function getTargetEntity()
    {
        return $this->target_entity;
    }
}