<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper;

use Everon\Helper;
use Everon\DataMapper\Exception;

class Criteria implements Interfaces\Criteria
{
    use Helper\ToArray;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $CriteriumCollection = null;

    /**
     * @var string
     */
    protected $glue = Criteria\Builder::GLUE_AND;

    
    /**
     * @return array
     */
    protected function getToArray($deep=false)
    {
        return $this->getCriteriumCollection()->toArray($deep);
    }

    /**
     * @inheritdoc
     */
    public function where(Interfaces\Criteria\Criterium $Criterium)
    {
        $Criterium->resetGlue(null);
        $this->getCriteriumCollection()->append($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function andWhere(Interfaces\Criteria\Criterium $Criterium)
    {
        if ($this->getCriteriumCollection()->isEmpty()) {
            throw new \Everon\DataMapper\Exception\Criteria('No subquery found, use where() to start new subqury');
        }
            
        $Criterium->glueByAnd();
        $this->getCriteriumCollection()->append($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orWhere(Interfaces\Criteria\Criterium $Criterium)
    {
        if ($this->getCriteriumCollection()->isEmpty()) {
            throw new \Everon\DataMapper\Exception\Criteria('No subquery found, use where() to start new subqury');
        }

        $Criterium->glueByOr();
        $this->getCriteriumCollection()->append($Criterium);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCriteriumCollection()
    {
        if ($this->CriteriumCollection === null) {
            $this->CriteriumCollection = new Helper\Collection([]);
        }
        
        return $this->CriteriumCollection;
    }

    /**
     * @inheritdoc
     */
    public function setCriteriumCollection($CriteriumCollection)
    {
        $this->CriteriumCollection = $CriteriumCollection;
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
    public function resetGlue()
    {
        $this->glue = null;
    }

    /**
     * @inheritdoc
     */
    public function glueByAnd()
    {
        $this->glue = Criteria\Builder::GLUE_AND;
    }

    /**
     * @inheritdoc
     */
    public function glueByOr()
    {
        $this->glue = Criteria\Builder::GLUE_OR;
    }
}