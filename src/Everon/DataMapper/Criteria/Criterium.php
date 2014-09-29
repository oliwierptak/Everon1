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
     * @var Interfaces\Criteria\Operator
     */
    protected $Operator = '=';
    
    /**
     * @var string
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $value = null;

    /**
     * @var mixed
     */
    protected $placeholder = null;

    /**
     * @var string
     */
    protected $glue = 'AND';
    
    
    public function __construct(Interfaces\Criteria\Operator $Operator, $column, $value)
    {
        $this->column = $column;
        $this->Operator = $Operator;
        $this->value = $value;
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
        return $this->Operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->Operator = $operator;
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

    public function toSql()
    {
        return sprintf("%s %s %s", $Criterium->getColumn(), $Criterium->getOperator(), $Criterium->getPlaceholder());
    }
    
    protected function getToString()
    {
        return $this->format();
        
    }
    
}