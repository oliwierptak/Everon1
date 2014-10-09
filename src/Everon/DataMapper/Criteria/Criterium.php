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

use Everon\Dependency;
use Everon\Helper;
use Everon\DataMapper\Exception;
use Everon\DataMapper\Interfaces;

class Criterium implements Interfaces\Criteria\Criterium
{
    use Dependency\Injection\Factory;
    
    use Helper\ToString;
    
    /**
     * @var string
     */
    protected $column = null;

    /**
     * @var string
     */
    protected $value = null;

    /**
     * @var string
     */
    protected $operator_type = null;

    /**
     * @var mixed
     */
    protected $placeholder = null;

    /**
     * @var string
     */
    protected $glue = 'AND';

    /**
     * @var \Everon\DataMapper\Interfaces\SqlPart
     */
    protected $SqlPart = null;
    
    
    public function __construct($column, $value, $operator_type)
    {
        $this->column = $column;
        $this->value = $value;
        $this->operator_type = $operator_type;
    }

    protected function buildOperator($column, $operator, $value)
    {
        $class = Builder::getOperatorClassName($operator);
        $Operator = $this->getFactory()->buildCriteriaOperator($class);

        //replace null values with IS NULL / IS NOT NULL
        if ($value === null) {
            if ($Operator->getType() === Operator::TYPE_EQUAL) {
                $class = Builder::getOperatorClassName(Operator::TYPE_IS);
                $Operator = $this->getFactory()->buildCriteriaOperator($class);
            }
            else if ($Operator->getType() === Operator::TYPE_NOT_EQUAL) {
                $class = Builder::getOperatorClassName(Operator::TYPE_NOT_IS);
                $Operator = $this->getFactory()->buildCriteriaOperator($class);
            }
        }

        return $Operator;
    }

    /**
     * @inheritdoc
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @inheritdoc
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function getGlue()
    {
        return $this->glue;
    }

    /**
     * @inheritdoc
     */
    public function setGlue($glue)
    {
        $this->glue = $glue;
    }

    /**
     * @inheritdoc
     */
    public function getPlaceholder()
    {
        if ($this->placeholder === null) {
            $this->placeholder = ':'.$this->getColumn();
        }
        
        return $this->placeholder;
    }

    /**
     * @inheritdoc
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @inheritdoc
     */
    public function getOperatorType()
    {
        return $this->operator_type;
    }

    /**
     * @inheritdoc
     */
    public function setOperatorType($operator)
    {
        $this->operator_type = $operator;
    }
    
    /**
     * @return Interfaces\SqlPart
     */
    public function getSqlPart()
    {
        return $this->SqlPart;
    }

    /**
     * @param Interfaces\SqlPart $SqlPart
     */
    public function setSqlPart(Interfaces\SqlPart $SqlPart)
    {
        $this->SqlPart = $SqlPart;
    }
    
    /**
     * @inheritdoc
     */
    public function toSqlPart()
    {
        if ($this->SqlPart === null) {
            $Operator = $this->buildOperator($this->getColumn(), $this->getOperatorType(), $this->getValue());
            list($sql, $parmetes) = $Operator->toSqlPartData($this);
            $this->SqlPart = $this->getFactory()->buildDataMapperSqlPart($sql, $parmetes);
        }
        
        return $this->SqlPart;
    }
}