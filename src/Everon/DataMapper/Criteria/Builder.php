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

    protected $operator_mapper = [
        Operator::TYPE_EQUAL => 'Equal',
        Operator::TYPE_LIKE => 'Like',
        /*
        self::TYPE_BETWEEN => 'OperatorBetween',
        self::TYPE_NOT_BETWEEN => 'OperatorNotBetween',
        self::TYPE_SMALLER_THAN => 'OperatorSmallerThan',
        self::TYPE_GREATER_THAN => 'OperatorGreaterThan',
        self::TYPE_GREATER_OR_EQUAL => 'OperatorGreaterOrEqual',
        self::TYPE_SMALLER_OR_EQUAL => 'OperatorSmallerOrEqual',
        self::TYPE_NOT_EQUAL => 'OperatorNotEqual',
        self::TYPE_IS => 'OperatorIs',
        self::TYPE_IS_NOT => 'OperatorIsNot',
        self::TYPE_IN => 'OperatorIn',
        self::TYPE_NOT_IN => 'OperatorNotIn'
        */
    ];
    
    /**
     * @var string
     */
    protected $current = null;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $CriteriaCollection = null;
    
    /**
     * @var string
     */
    protected $glue = 'AND';


    /**
     * @inheritdoc
     */
    public function andWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCriteria()->andWhere($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCriteria()->orWhere($Criterium);
        return $this;
    }

    /**
     * @return Interfaces\Criteria
     */
    public function getCriteria()
    {
        if ($this->CriteriaCollection === null) {
            $Criteria = $this->getFactory()->buildCriteria();
            $this->CriteriaCollection = new Helper\Collection([$Criteria]);
            $this->current = 0;
        }
        
        return $this->CriteriaCollection[$this->current];
    }

    /**
     * @param Interfaces\Criteria $Criteria
     */
    public function setCriteria(Interfaces\Criteria $Criteria)
    {
        $this->Criteria = $Criteria;
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
        $sql = $this->criteriaToSql($this->getCriteria());
        
        sd($or_sql);
    }
    
    protected function criteriaToSql(Interfaces\Criteria $Criteria)
    {
        $and_sql = '';
        foreach ($Criteria->getCriteriumCollection() as $Criterium) {
            $Operator = $this->buildOperator($Criterium);
            $and_sql .= $Operator->toSql() . ' '.$Criteria->getGlue().' ';
        }
        
        return '('.rtrim($and_sql, ' '.$Criteria->getGlue()).')';
    }

    /**
     * @param Interfaces\Criteria\Criterium $Criterium
     * @return Interfaces\Criteria\Operator
     * @throws Exception\CriteriaBuilder
     */
    protected function buildOperator(Interfaces\Criteria\Criterium $Criterium)
    {
        $operator = strtoupper($Criterium->getOperator());
        if (isset($this->operator_mapper[$operator]) === false) {
            throw new \Everon\DataMapper\Exception\CriteriaBuilder('Unsupported criteria operator for: "%s"', $operator);
        }
        
        $type = $this->operator_mapper[$operator];
        return $this->getFactory()->buildCriteriaOperator($type);
    }
}