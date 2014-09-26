<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Criteria;

use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces;

class Criterium implements Interfaces\Criteria\Criterium
{
    use Helper\ToString;
    
    /**
     * @var string
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $operator = '=';

    /**
     * @var string
     */
    protected $value = null;

    /**
     * @var mixed
     */
    protected $placeholder = null;
    
    
    public function __construct($column, $operator, $value)
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
    }
    
    protected function format()
    {
        //todo: other way around put criterium into operator and move format there, where to put values?
        //$Operator = new...
        //$Operator->getSqlPart()
        return sprintf("%s %s %s", $this->getColumn(), $this->getOperator(), $this->getPlaceholder());
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
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
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getPlaceholder()
    {
        if ($this->placeholder === null) {
            $this->placeholder = ':'.$this->getColumn();
        }
        
        return $this->placeholder;
    }

    /**
     * @param mixed $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }
    
    protected function getToString()
    {
        return $this->format();
    }
    
}