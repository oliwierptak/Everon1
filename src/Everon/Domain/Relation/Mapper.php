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
    protected $type = null;
    
    /**
     * @var string
     */
    protected $domain_name = null;

    /**
     * @var string
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $mapped_by = null;

    /**
     * @var string
     */
    protected $inversed_by = null;

    /**
     * @var bool
     */
    protected $is_virtual = false;

    /**
     * @var bool
     */
    protected $is_owning_side = null;
    
    
    public function __construct($type, $domain_name, $column=null, $mapped_by=null, $inversed_by=null, $is_virtual=false)
    {
        $this->type = $type;
        $this->domain_name = $domain_name;
        $this->column = $column;
        $this->mapped_by = $mapped_by;
        $this->inversed_by = $inversed_by;
        $this->is_virtual = $is_virtual;
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
    public function setDomainName($target_entity)
    {
        $this->domain_name = $target_entity;
    }

    /**
     * @return string
     */
    public function getDomainName()
    {
        return $this->domain_name;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param boolean $is_virtual
     */
    public function setIsVirtual($is_virtual)
    {
        $this->is_virtual = $is_virtual;
    }

    /**
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->is_virtual;
    }

    /**
     * @param boolean $is_owning_side
     */
    public function setIsOwningSide($is_owning_side)
    {
        $this->is_owning_side = $is_owning_side;
    }

    /**
     * @return boolean
     */
    public function isOwningSide()
    {
        if ($this->is_owning_side === null) {
            switch ($this->getType()) {
                case \Everon\Domain\Relation::ONE_TO_ONE:
                    $this->is_owning_side = $this->getMappedBy() !== null && $this->getInversedBy() === null;
                    break;
                
                case \Everon\Domain\Relation::ONE_TO_MANY:
                    $this->is_owning_side = false;
                    break;

                case \Everon\Domain\Relation::MANY_TO_ONE:
                    $this->is_owning_side = true;
                    break;

                case \Everon\Domain\Relation::MANY_TO_MANY:
                    $this->is_owning_side = false;
                    break;
            }
             
        }
        
        return $this->is_owning_side;
    }
    
}