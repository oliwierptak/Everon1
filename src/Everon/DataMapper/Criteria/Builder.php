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
     * @var \Everon\Interfaces\Collection
     */
    protected $CriteriaCollection = null;
    
    protected $glue = 'AND';
    
    public function _and($column, $operator, $value)//Interfaces\Criteria $Criteria)
    {
        $Criteria->setGlue('AND');
        $this->getCriteriaCollection()->get('AND')->append($Criteria);
        return $this;
    }
    
    public function _or($column, $operator, $value)//Interfaces\Criteria $Criteria)
    {
        $Criteria = $this->getFactory()->buildCriteria($column, $operator, $value);
        $Criteria->setGlue('OR');
        $this->getCriteriaCollection()->get('OR')->append($Criteria);
        return $this;
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    public function getCriteriaCollection()
    {
        if ($this->CriteriaCollection === null) {
            $this->CriteriaCollection = new Helper\Collection([
                'AND' => new Helper\Collection([]),
                'OR' => new Helper\Collection([]),
            ]);
        }

        return $this->CriteriaCollection;
    }

    /**
     * @param \Everon\Interfaces\Collection $CriteriaCollection
     */
    public function setCriteriaCollection($CriteriaCollection)
    {
        $this->CriteriaCollection = $CriteriaCollection;
    }

}