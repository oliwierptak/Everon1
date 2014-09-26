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

    const OPERATOR_TYPE_IN = 'IN';
    const OPERATOR_TYPE_EQUAL = '=';

    /**
     * @var Interfaces\Criteria
     */
    protected $CriteriaOr = null;

    /**
     * @var Interfaces\Criteria
     */
    protected $CriteriaAnd = null;

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
        $this->getCriteriaAnd()->_and($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhere($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCriteriaOr()->_or($Criterium);
        return $this;
    }
    
    /**
     * @return Interfaces\Criteria
     */
    public function getCriteriaAnd()
    {
        if ($this->CriteriaAnd === null) {
            $this->CriteriaAnd = $this->getFactory()->buildCriteria();
            $this->CriteriaAnd->setGlue('AND');
        }

        return $this->CriteriaAnd;
    }

    /**
     * @param Interfaces\Criteria $CriteriaAnd
     */
    public function setCriteriaAnd(Interfaces\Criteria $CriteriaAnd)
    {
        $this->CriteriaAnd = $CriteriaAnd;
    }

    /**
     * @return Interfaces\Criteria
     */
    public function getCriteriaOr()
    {
        if ($this->CriteriaOr === null) {
            $this->CriteriaOr = $this->getFactory()->buildCriteria();
            $this->CriteriaOr->setGlue('OR');
        }
        
        return $this->CriteriaOr;
    }

    /**
     * @param Interfaces\Criteria $CriteriaOr
     */
    public function setCriteriaOr(Interfaces\Criteria $CriteriaOr)
    {
        $this->CriteriaOr = $CriteriaOr;
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
    
    public function toSql()
    {
        $and_sql = $this->criteriaToSql($this->getCriteriaAnd());
        $or_sql = $this->criteriaToSql($this->getCriteriaOr());
        
        sd($and_sql, $or_sql);
    }
    
    protected function criteriaToSql(Interfaces\Criteria $Criteria)
    {
        $and_sql = '';
        foreach ($Criteria->getCriteriumCollection() as $Criterium) {
            $Operator = $this->getFactory()->buildCriteriaOperator($Criterium);
            $and_sql .= $Operator->toSql() . ' '.$Criteria->getGlue().' ';
        }
        
        return '('.rtrim($and_sql, ' '.$Criteria->getGlue()).')';
    }
}