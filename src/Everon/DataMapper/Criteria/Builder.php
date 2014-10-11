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

class Builder implements Interfaces\Criteria\Builder
{
    use Dependency\Injection\Factory;
    
    use Helper\Arrays;
    use Helper\ToArray;
    use Helper\ToString;

    const GLUE_AND = 'AND';
    const GLUE_OR = 'OR';

    protected static $operator_mappers = [
        Operator::SQL_EQUAL => Operator::TYPE_EQUAL,
        Operator::SQL_NOT_EQUAL => Operator::TYPE_NOT_EQUAL,
        Operator::SQL_LIKE => Operator::TYPE_LIKE,
        Operator::SQL_IN => Operator::TYPE_IN,
        Operator::SQL_NOT_IN => Operator::TYPE_NOT_IN,
        Operator::SQL_IS => Operator::TYPE_IS,
        Operator::SQL_NOT_IS => Operator::TYPE_NOT_IS,
        Operator::SQL_GREATER_OR_EQUAL => Operator::TYPE_GREATER_OR_EQUAL,
        
        /*
        self::TYPE_BETWEEN => 'OperatorBetween',
        self::TYPE_NOT_BETWEEN => 'OperatorNotBetween',
        self::TYPE_SMALLER_THAN => 'OperatorSmallerThan',
        self::TYPE_GREATER_THAN => 'OperatorGreaterThan',
        self::TYPE_SMALLER_OR_EQUAL => 'OperatorSmallerOrEqual',
        */
    ];
    
    /**
     * @var string
     */
    protected $current = -1;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $CriteriaCollection = null;
    
    /**
     * @var string
     */
    protected $glue = self::GLUE_AND;

    
    public function __construct()
    {
        $this->CriteriaCollection = new Helper\Collection([]);
    }

    /**
     * @return array
     */
    protected function getToArray()
    {
        return $this->getCriteriaCollection()->toArray();
    }

    /**
     * @param Interfaces\Criteria $Criteria
     * @return string
     */
    protected function criteriaToSql(Interfaces\Criteria $Criteria)
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Criteria\Criterium $Criterium
         */
        $sql = '';
        foreach ($Criteria->getCriteriumCollection() as $Criterium) {
            $SqlPart = $Criterium->toSqlPart();
            $sql .= $SqlPart->getSql() . ' '.$Criteria->getGlue().' ';
        }

        return '('.rtrim($sql, ' '.$Criteria->getGlue()).')';
    }

    /**
     * @param Interfaces\Criteria $Criteria
     * @return array
     */
    protected function criteriaToParameters(Interfaces\Criteria $Criteria)
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Criteria\Criterium $Criterium
         */
        $parameters = [];
        foreach ($Criteria->getCriteriumCollection() as $Criterium) {
            $SqlPart = $Criterium->toSqlPart();
            $parameters[] = $SqlPart->getParameters();
        }

        return $parameters;
    }

    /**
     * @inheritdoc
     */
    public function where($column, $operator, $value)
    {
        $this->current++;
        $Criterium = $this->getFactory()->buildDataMapperCriterium($column, $operator, $value);
        $this->getCurrentCriteria()->where($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function andWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildDataMapperCriterium($column, $operator, $value);
        $this->getCurrentCriteria()->andWhere($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildDataMapperCriterium($column, $operator, $value);
        $this->getCurrentCriteria()->orWhere($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentCriteria()
    {
        if ($this->CriteriaCollection->has($this->current) === false) {
            $Criteria = $this->getFactory()->buildDataMapperCriteria();
            $this->CriteriaCollection[$this->current] = $Criteria; 
        }
        
        return $this->CriteriaCollection[$this->current];
    }

    /**
     * @inheritdoc
     */
    public function setCurrentCriteria(Interfaces\Criteria $Criteria)
    {
        $this->CriteriaCollection[$this->current] = $Criteria;
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
    public function getCriteriaCollection()
    {
        return $this->CriteriaCollection;
    }

    /**
     * @inheritdoc
     */
    public function setCriteriaCollection(\Everon\Interfaces\Collection $CriteriaCollection)
    {
        $this->CriteriaCollection = $CriteriaCollection;
    }

    /**
     * @inheritdoc
     */
    public function toSqlPart()
    {
        $sql = [];
        $parameters = [];
        
        foreach ($this->getCriteriaCollection() as $Criteria) {
            $sql[] = $this->criteriaToSql($Criteria);
            $criteria_parameters = $this->criteriaToParameters($Criteria);
            $tmp = [];
            
            foreach ($criteria_parameters as $cp_value) {
                $tmp = $this->arrayMergeDefault($tmp, $cp_value);
            }

            $parameters = $this->arrayMergeDefault($tmp, $parameters);
        }
        
        $sql = implode(' '.$this->getGlue().' ', $sql);
        $sql = rtrim($sql, $this->getGlue().' ');
        
        return $this->getFactory()->buildDataMapperSqlPart($sql, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function glueByAnd()
    {
        $this->glue = self::GLUE_AND;
    }

    /**
     * @inheritdoc
     */
    public function glueByOr()
    {
        $this->glue = self::GLUE_OR;
    }

    /**
     * @inheritdoc
     */
    public static function getOperatorClassNameBySqlOperator($operator)
    {
        $operator = strtoupper(trim($operator));
        if (isset(static::$operator_mappers[$operator]) === false) {
            throw new Exception\CriteriaBuilder('Unknown operator_type type: "%s"', $operator);
        }
        
        return static::$operator_mappers[$operator];
    }

    /**
     * @inheritdoc
     */
    public static function randomizeParameterName($name)
    {
        return $name.'_'.mt_rand(100, time());
    }
    
}