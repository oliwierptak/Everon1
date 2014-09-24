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

    /**
     * @var Interfaces\Criteria
     */
    protected $CriteriaOr = null;

    /**
     * @var Interfaces\Criteria
     */
    protected $CriteriaAnd = null;


    /**
     * @inheritdoc
     */
    public function _and($column, $operator, $value)
    {
        $Criterium = $this->getFactory()->buildCriterium($column, $operator, $value);
        $this->getCriteriaAnd()->_and($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function _or($column, $operator, $value)
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
    
    public function toSql()
    {
        $or_sql = '';
        $and_sql = '';
        $and_criteria = $this->getCriteriaAnd();
        
        foreach ($this->getCriteriaAnd()->getCriteriumCollection() as $Criterium) {
            s($Criterium);
        }
    }
}