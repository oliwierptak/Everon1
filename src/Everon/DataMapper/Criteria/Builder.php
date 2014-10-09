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
        Operator::TYPE_EQUAL => 'Equal',
        Operator::TYPE_NOT_EQUAL => 'NotEqual',
        Operator::TYPE_LIKE => 'Like',
        Operator::TYPE_IN => 'In',
        Operator::TYPE_NOT_IN => 'NotIn',
        Operator::TYPE_IS => 'Is',
        Operator::TYPE_NOT_IS => 'NotIs',
        /*
        self::TYPE_BETWEEN => 'OperatorBetween',
        self::TYPE_NOT_BETWEEN => 'OperatorNotBetween',
        self::TYPE_SMALLER_THAN => 'OperatorSmallerThan',
        self::TYPE_GREATER_THAN => 'OperatorGreaterThan',
        self::TYPE_GREATER_OR_EQUAL => 'OperatorGreaterOrEqual',
        self::TYPE_SMALLER_OR_EQUAL => 'OperatorSmallerOrEqual',
        self::TYPE_NOT_EQUAL => 'OperatorNotEqual',
        self::TYPE_IS => 'OperatorIs',
        self::TYPE_NOT_IS => 'OperatorIsNot',
        self::TYPE_IN => 'OperatorIn',
        self::TYPE_NOT_IN => 'OperatorNotIn'
        */
    ];
    
    /**
     * @var string
     */
    public $current = -1;

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
    
    public function where($column, $operator, $value)
    {
        $this->current++;
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCurrentCriteria()->where($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function andWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCurrentCriteria()->andWhere($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCurrentCriteria()->orWhere($Criterium);
        return $this;
    }

    /**
     * @return Interfaces\Criteria
     */
    public function getCurrentCriteria()
    {
        if ($this->CriteriaCollection->has($this->current) === false) {
            $Criteria = $this->getFactory()->buildCriteria();
            $this->CriteriaCollection[$this->current] = $Criteria; 
        }
        
        return $this->CriteriaCollection[$this->current];
    }

    /**
     * @param Interfaces\Criteria $Criteria
     */
    public function setCurrentCriteria(Interfaces\Criteria $Criteria)
    {
        $this->CriteriaCollection[$this->current] = $Criteria;
    }
    
    /**
     * @return string
     */
    public function getGlue()
    {
        return $this->glue;
    }

    /**
     * @param string $glue
     */
    public function setGlue($glue)
    {
        $this->glue = $glue;
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    public function getCriteriaCollection()
    {
        return $this->CriteriaCollection;
    }

    /**
     * @param \Everon\Interfaces\Collection $CriteriaCollection
     */
    public function setCriteriaCollection(\Everon\Interfaces\Collection $CriteriaCollection)
    {
        $this->CriteriaCollection = $CriteriaCollection;
    }
    
    public function toSql()
    {
        $sql = [];
        $params = [];
        
        foreach ($this->getCriteriaCollection() as $Criteria) {
            $sql[] = $this->criteriaToSql($Criteria);
            $params[] = $this->criteriaToParameters($Criteria);
        }
        
        
        
        sd($sql, $params);
    }

    public function glueByAnd()
    {
        $this->glue = self::GLUE_AND;
    }
    
    public function glueByOr()
    {
        $this->glue = self::GLUE_OR;
    }

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
     * @param $operator
     * @return string
     * @throws Exception\CriteriaBuilder
     */
    public static function getOperatorClassName($operator)
    {
        $operator = strtoupper(trim($operator));
        if (isset(static::$operator_mappers[$operator]) === false) {
            throw new Exception\CriteriaBuilder('Unknown operator_type type: "%s"', $operator);
        }
        
        return static::$operator_mappers[$operator];
    }
}