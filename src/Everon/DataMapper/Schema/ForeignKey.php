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

class ForeignKey extends Constraint implements Schema\ForeignKey 
{
    protected $foreign_schema_name = null;
    
    protected $foreign_table_name = null;
    
    protected $foreign_column_name = null;
    
    protected $column_name = null;


    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->unlock();

        $PrimaryKeyInfo = new Helper\PopoProps($data);
        $this->foreign_schema_name = $PrimaryKeyInfo->foreign_schema_name;
        $this->foreign_table_name = $PrimaryKeyInfo->foreign_table_name;
        $this->foreign_column_name = $PrimaryKeyInfo->foreign_column_name;
        $this->column_name = $PrimaryKeyInfo->column_name;
        
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
     * @inheritdoc
     */
    public function getColumnName()
    {
        return $this->column_name;
    }

    /**
     * @param $referenced_table_name
     */
    public function setForeignTableName($referenced_table_name)
    {
        $this->foreign_table_name = $referenced_table_name;
    }

    /**
     * @return string
     */
    public function getForeignTableName()
    {
        return $this->foreign_table_name;
    }

    /**
     * @param $referenced_column_name
     */
    public function setForeignColumnName($referenced_column_name)
    {
        $this->foreign_column_name = $referenced_column_name;
    }

    /**
     * @return string
     */
    public function getForeignColumnName()
    {
        return $this->foreign_column_name;
    }

    /**
     * @param null $referenced_schema_name
     */
    public function setForeignSchemaName($referenced_schema_name)
    {
        $this->foreign_schema_name = $referenced_schema_name;
    }

    /**
     * @return null
     */
    public function getForeignSchemaName()
    {
        return $this->foreign_schema_name;
    }

    /**
     * @return string
     */
    public function getForeignFullTableName()
    {
        return $this->getForeignSchemaName().'.'.$this->getForeignTableName();
    }
    
}
