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

use Everon\DataMapper\Interfaces\Schema;
use Everon\Domain\Interfaces\Entity;
use Everon\Helper;

class Table implements Schema\Table
{
    use Helper\Immutable;
    
    
    protected $name = null;
    
    protected $pk = null;
    
    protected $columns = array();
    
    protected $primary_keys = array();
    
    protected $constraints = array();

    protected $foreign_keys = array();


    /**
     * @param $name
     * @param array $columns
     * @param array $constraints
     * @param array $foreign_keys
     */
    public function __construct($name, array $columns, array $constraints, array $foreign_keys) //todo: the arrays should be collections
    {
        $this->name = $name;
        $this->columns = $columns;
        $this->constraints = $constraints;
        $this->foreign_keys = $foreign_keys;
        
        $this->init();
        $this->lock();
    }
    
    protected function init()
    {
        /**
         * @var Schema\Column $Column
         */
        foreach ($this->columns as $Column) {
            if ($Column->isPk()) {
                $this->pk = $Column->getName();
            }
        }
    }
    
    public function getPlaceholderForQuery($delimeter=':')
    {
        $placeholders = array();
        foreach ($this->columns as $column_name) {
            $placeholders[] = $delimeter.$column_name;
        }
        
        return $placeholders;
    }

    /**
     * @param Entity $Entity
     * @return array
     */
    public function getValuesForQuery(Entity $Entity)
    {
        $values = array();
        foreach ($this->columns as $column_name) {
            $values[] = $Entity->getValueByName($column_name);
        }
        
        return $values;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getConstraints()
    {
        return $this->constraints;
    }
    
    public function toArray()
    {
        return get_object_vars($this);
    }
    
    public function getPk()
    {
        return $this->pk;
    }
}
