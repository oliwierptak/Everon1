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

use Everon\DataMapper\Interfaces;
use Everon\Dependency;
use Everon\Interfaces\PdoAdapter;
use Everon\Helper;

abstract class Reader implements Interfaces\Schema\Reader
{
    use Dependency\PdoAdapter;
    use Helper\Arrays;
    
    
    protected $name = null;
    
    protected $column_list = null;
    
    protected $constraint_list = null;
    
    protected $foreign_key_list = null;
    
    protected $table_list = null;
    
    
    abstract protected function getTablesSql();
    abstract protected function getColumnsSql();
    abstract protected function getConstraintsSql();
    abstract protected function getForeignKeysSql();


    /**
     * @param $name
     * @param PdoAdapter $PdoAdapter
     */
    public function __construct($name, PdoAdapter $PdoAdapter)
    {
        $this->name = $name;
        $this->PdoAdapter = $PdoAdapter;
    }

    /**
     * @inheritdoc
     */
    public function getDriver()
    {
        return $this->getPdoAdapter()->getConnectionConfig()->getDriver();
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getTableList()
    {
        if ($this->table_list === null) {
            $this->table_list = $this->arrayArrangeByKey('TABLE_NAME',
                $this->getPdoAdapter()->execute($this->getTablesSql(), ['schema'=>$this->getName()], \PDO::FETCH_ASSOC)
            );
        }
        return $this->table_list;
    }

    /**
     * @inheritdoc
     */
    public function getColumnList()
    {
        if ($this->column_list === null) {
            $this->column_list = $this->arrayArrangeByKey('TABLE_NAME',
                $this->getPdoAdapter()->execute($this->getColumnsSql(), ['schema'=>$this->getName()], \PDO::FETCH_ASSOC)
            );
        }
        return $this->column_list;
    }

    /**
     * @inheritdoc
     */
    public function getConstraintList()
    {
        if ($this->constraint_list === null) {
            $this->constraint_list = $this->arrayArrangeByKey('TABLE_NAME',
                $this->getPdoAdapter()->execute($this->getConstraintsSql(), ['schema'=>$this->getName()], \PDO::FETCH_ASSOC)
            );
        }
        return $this->constraint_list;
    }

    /**
     * @inheritdoc
     */
    public function getForeignKeyList()
    {
        if ($this->foreign_key_list === null) {
            $this->foreign_key_list = $this->arrayArrangeByKey('TABLE_NAME',
                $this->getPdoAdapter()->execute($this->getForeignKeysSql(), ['schema'=>$this->getName()], \PDO::FETCH_ASSOC)    
            );
        }
        return $this->foreign_key_list;
    }
}