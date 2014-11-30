<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema;

use Everon\Helper;
use Everon\DataMapper\Interfaces\Schema;

abstract class Constraint implements Schema\Constraint 
{
    use Helper\Immutable;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $schema = null;

    /**
     * @var string
     */
    protected $table_name = null;

    /**
     * @var string
     */
    protected $column_name = null;
    
    
    public function __construct(array $data)
    {
        $ConstraintInfo = new Helper\PopoProps($data);

        $this->name = $ConstraintInfo->constraint_name;
        $this->schema = $ConstraintInfo->constraint_schema;
        $this->table_name = $ConstraintInfo->table_name;
        $this->column_name = $ConstraintInfo->column_name;

        $this->lock();
    }

    /**
     * @param string $column_name
     */
    public function setColumnName($column_name)
    {
        $this->column_name = $column_name;
    }

    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->column_name;
    }
    
    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $table_name
     */
    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * @return null
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getFullTableName()
    {
        return $this->getSchema().'.'.$this->getTableName();
    }
    
    public function toArray($deep=false)
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
