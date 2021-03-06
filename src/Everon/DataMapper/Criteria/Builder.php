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
        Operator::SQL_NOT_LIKE => Operator::TYPE_LIKE,
        Operator::SQL_ILIKE => Operator::TYPE_ILIKE,
        Operator::SQL_NOT_ILIKE => Operator::TYPE_ILIKE,
        Operator::SQL_IN => Operator::TYPE_IN,
        Operator::SQL_NOT_IN => Operator::TYPE_NOT_IN,
        Operator::SQL_IS => Operator::TYPE_IS,
        Operator::SQL_NOT_IS => Operator::TYPE_NOT_IS,
        Operator::SQL_GREATER_THEN => Operator::TYPE_GREATER_THEN,
        Operator::SQL_GREATER_OR_EQUAL => Operator::TYPE_GREATER_OR_EQUAL,
        Operator::SQL_SMALLER_THEN => Operator::TYPE_SMALLER_THEN,
        Operator::SQL_SMALLER_OR_EQUAL => Operator::TYPE_SMALLER_OR_EQUAL,
        Operator::SQL_BETWEEN => Operator::TYPE_BETWEEN,
        Operator::SQL_NOT_BETWEEN => Operator::TYPE_NOT_BETWEEN,
        Operator::SQL_RAW => Operator::TYPE_RAW
    ];
    
    /**
     * @var string
     */
    protected $current = -1;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $ContainerCollection = null;

    /**
     * @var string
     */
    protected $glue = self::GLUE_AND;

    /**
     * @var int
     */
    protected $offset = null;

    /**
     * @var int
     */
    protected $limit = null;

    /**
     * @var array
     */
    protected $order_by = [];

    /**
     * @var string
     */
    protected $group_by = null;

    
    public function __construct()
    {
        $this->ContainerCollection = new Helper\Collection([]);
    }

    /**
     * @return array
     */
    protected function getToArray()
    {
        $SqlPart = $this->toSqlPart();
        return $SqlPart->getParameters();
    }

    /**
     * @return string
     */
    protected function getToString()
    {
        $SqlPart = $this->toSqlPart();
        return $SqlPart->getSql();
    }

    /**
     * @param Interfaces\Criteria\Container $Container
     * @return string
     */
    protected function criteriaToSql(Interfaces\Criteria\Container $Container)
    {
        /**
         * @var Interfaces\Criteria\Criterium $Criterium
         */
        $sql = '';
        foreach ($Container->getCriteria()->getCriteriumCollection() as $Criterium) {
            $SqlPart = $Criterium->getSqlPart();
            $sql .= ltrim($Criterium->getGlue().' '.$SqlPart->getSql().' ');
        }

        return '('.rtrim($sql).')';
    }

    /**
     * @param Interfaces\Criteria\Container $Container
     * @return array
     */
    protected function criteriaToParameters(Interfaces\Criteria\Container $Container)
    {
        /**
         * @var Interfaces\Criteria\Criterium $Criterium
         */
        $parameters = [];
        foreach ($Container->getCriteria()->getCriteriumCollection() as $Criterium) {
            $SqlPart = $Criterium->getSqlPart();
            $parameters[] = $SqlPart->getParameters();
        }

        return $parameters;
    }
    
    /**
     * @inheritdoc
     */
    public function where($column, $operator, $value, $glue = self::GLUE_AND)
    {
        $this->current++;
        $Criterium = $this->getFactory()->buildCriteriaCriterium($column, $operator, $value);
        $this->getCurrentContainer()->getCriteria()->where($Criterium);

        if ($this->current > 0) {
            $this->getCurrentContainer()->setGlue($glue);
        }
        else {
            $this->getCurrentContainer()->resetGlue(); //reset glue for first item
        }
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function andWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriteriaCriterium($column, $operator, $value);
        if ($this->current < 0) {
            $this->where($column, $operator, $value);
        }
        else {
            $this->getCurrentContainer()->getCriteria()->andWhere($Criterium);
        }
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriteriaCriterium($column, $operator, $value);
        if ($this->current < 0) {
            $this->where($column, $operator, $value);
        }
        else {
            $this->getCurrentContainer()->getCriteria()->orWhere($Criterium);
        }
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function whereRaw($sql, array $value = null, $glue = \Everon\DataMapper\Criteria\Builder::GLUE_AND)
    {
        $this->current++;
        $Criterium = $this->getFactory()->buildCriteriaCriterium($sql, 'raw', $value);
        $this->getCurrentContainer()->getCriteria()->where($Criterium);

        if ($this->current > 0) {
            $this->getCurrentContainer()->setGlue($glue);
        }
        else {
            $this->getCurrentContainer()->resetGlue(); //reset glue for first item
        }
        
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function andWhereRaw($sql, array $value = null)
    {
        $Criterium = $this->getFactory()->buildCriteriaCriterium($sql, 'raw', $value);
        if ($this->current < 0) {
            $this->where($sql, 'raw', $value);
        }
        else {
            $this->getCurrentContainer()->getCriteria()->andWhere($Criterium);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhereRaw($sql, array $value = null)
    {
        $Criterium = $this->getFactory()->buildCriteriaCriterium($sql, 'raw', $value);
        if ($this->current < 0) {
            $this->where($sql, 'raw', $value);
        }
        else {
            $this->getCurrentContainer()->getCriteria()->orWhere($Criterium);
        }
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getCurrentContainer()
    {
        if ($this->ContainerCollection->has($this->current) === false) {
            $Criteria = $this->getFactory()->buildCriteria();
            $Container = $this->getFactory()->buildCriteriaContainer($Criteria, null);
            $this->ContainerCollection[$this->current] = $Container; 
        }
        
        return $this->ContainerCollection[$this->current];
    }

    /**
     * @inheritdoc
     */
    public function setCurrentContainer(Interfaces\Criteria\Container $Container)
    {
        $this->ContainerCollection[$this->current] = $Container;
    }

    /**
     * @inheritdoc
     */
    public function getContainerCollection()
    {
        return $this->ContainerCollection;
    }

    /**
     * @inheritdoc
     */
    public function setContainerCollection(\Everon\Interfaces\Collection $ContainerCollection)
    {
        $this->ContainerCollection = $ContainerCollection;
    }

    /**
     * @inheritdoc
     */
    public function getGlue()
    {
        return $this->getCurrentContainer()->getGlue();
    }

    /**
     * @inheritdoc
     */
    public function resetGlue()
    {
        $this->getCurrentContainer()->resetGlue();
    }

    /**
     * @inheritdoc
     */
    public function glueByAnd()
    {
        $this->getCurrentContainer()->glueByAnd();
    }

    /**
     * @inheritdoc
     */
    public function glueByOr()
    {
        $this->getCurrentContainer()->glueByOr();
    }

    /**
     * @inheritdoc
     */
    public function getGroupBy()
    {
        return $this->group_by;
    }

    /**
     * @inheritdoc
     */
    public function setGroupBy($group_by)
    {
        $this->group_by = $group_by;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @inheritdoc
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @inheritdoc
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrderBy()
    {
        return $this->order_by;
    }

    /**
     * @inheritdoc
     */
    public function setOrderBy(array $order_by)
    {
        $this->order_by = $order_by;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOffsetLimitSql()
    {
        if ($this->getLimit() === null && $this->getOffset() === null) {
            return '';
        }

        if ($this->getLimit() === null && ($this->getOffset() !== null && (int) $this->getOffset() !== 0)) {
            return 'OFFSET '.$this->offset;
        }

        if ((int) $this->getLimit() !== 0 && $this->getOffset() === null) {
            return 'LIMIT '.$this->getLimit();
        }

        return 'LIMIT '.$this->getLimit(). ' OFFSET '.$this->getOffset();
    }

    /**
     * @inheritdoc
     */
    public function getOrderByAndSortSql()
    {
        if (is_array($this->getOrderBy()) === false || empty($this->getOrderBy())) {
            return '';
        }

        $order_by = '';
        foreach ($this->getOrderBy() as $name => $sort) {
            $order_by .= "${name} ".$sort.',';
        }

        if ($order_by !== '') {
            $order_by = trim($order_by, ',');
            $order_by = 'ORDER BY '.$order_by;
        }

        return $order_by;
    }

    /**
     * @inheritdoc
     */
    public function getGroupBySql()
    {
        if ($this->getGroupBy() === null) {
            return '';
        }

        return 'GROUP BY '.$this->getGroupBy();
    }

    /**
     * @inheritdoc
     */
    public function toSqlPart()
    {
        $sql = [];
        $parameters = [];
        $glue = null;

        /**
         * @var Interfaces\Criteria $Container
         */
        foreach ($this->getContainerCollection() as $Container) {
            $glue = (count($sql) === 0) ? '' : $Container->getGlue().' '; //reset glue if that's the first iteration
            
            $sql[] = $glue.$this->criteriaToSql($Container);
            $criteria_parameters = $this->criteriaToParameters($Container);
            $tmp = [];

            foreach ($criteria_parameters as $cp_value) {
                $tmp = $this->arrayMergeDefault($tmp, $cp_value);
            }

            $parameters = $this->arrayMergeDefault($tmp, $parameters);
        }

        $sql_query = implode("\n", $sql);
        $sql_query = rtrim($sql_query, $glue.' ');

        $sql_query .= ' '.trim($this->getGroupBySql().' '.
                $this->getOrderByAndSortSql().' '.
                $this->getOffsetLimitSql());

        $sql_query = empty($sql) === false ? 'WHERE '.$sql_query : $sql_query;

        return $this->getFactory()->buildDataMapperSqlPart(trim($sql_query), $parameters);
    }

    /**
     * @inheritdoc
     */
    public function appendContainerCollection(\Everon\Interfaces\Collection $ContainerCollectionToMerge, $glue=self::GLUE_AND)
    {
        /**
         * @var \Everon\DataMapper\Interfaces\Criteria\Container $ContainerToMerge
         */
        foreach ($ContainerCollectionToMerge as $ContainerToMerge) {
            if ($ContainerToMerge->getGlue() === null) {
                $ContainerToMerge->setGlue($glue);
            }
            $this->getContainerCollection()->append($ContainerToMerge);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getOperatorClassNameBySqlOperator($operator)
    {
        $operator = strtoupper(trim($operator));
        if (isset(static::$operator_mappers[$operator]) === false) {
            throw new Exception\CriteriaBuilder('Unknown operator type: "%s"', $operator);
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