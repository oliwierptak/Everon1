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

    use Helper\ToArray;
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
    protected $glue = null;

    /**
     * @var \Everon\DataMapper\Interfaces\SqlPart
     */
    protected $SqlPart = null;


    /**
     * @param $column
     * @param $value
     * @param $operator_type
     */
    public function __construct($column, $operator_type, $value)
    {
        $this->column = $column;
        $this->operator_type = $operator_type;
        $this->value = $value;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return Interfaces\Criteria\Operator
     * @throws Exception\CriteriaBuilder
     */
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
    public function glueByAnd()
    {
        $this->glue = Builder::GLUE_AND;
    }

    /**
     * @inheritdoc
     */
    public function glueByOr()
    {
        $this->glue = Builder::GLUE_OR;
    }

    /**
     * @inheritdoc
     */
    public function resetGlue()
    {
        $this->glue = null;
    }

    /**
     * @inheritdoc
     */
    public function getPlaceholder()
    {
        if ($this->value === null) {
            return 'NULL';
        }
        
        if ($this->placeholder === null) {
            $this->placeholder = ':'.Builder::randomizeParameterName($this->getColumn());
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
    public function getPlaceholderAsParameter()
    {
        return ltrim($this->getPlaceholder(), ':');
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
     * @inheritdoc
     */
    public function getSqlPart()
    {
        return $this->SqlPart;
    }

    /**
     * @inheritdoc
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
            list($sql, $parameters) = $Operator->toSqlPartData($this);
            $this->SqlPart = $this->getFactory()->buildDataMapperSqlPart($sql, $parameters);
        }
        
        return $this->SqlPart;
    }
}