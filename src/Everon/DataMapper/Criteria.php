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
    use Helper\Arrays;
    use Helper\ToArray;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $CriteriumCollection = null;

    /**
     * @var string
     */
    protected $glue = 'AND';


    public function _and(Interfaces\Criteria\Criterium $Criterium)
    {
        $Criterium->setGlue('AND');
        
        $this->getCriteriumCollection()->append($Criterium);
        return $this;
    }

    public function _or(Interfaces\Criteria\Criterium $Criterium)
    {
        $Criterium->setGlue('OR');

        $this->getCriteriumCollection()->append($Criterium);
        return $this;
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    public function getCriteriumCollection()
    {
        if ($this->CriteriumCollection === null) {
            $this->CriteriumCollection = new Helper\Collection([]);
        }
        
        return $this->CriteriumCollection;
    }

    /**
     * @param \Everon\Interfaces\Collection $CriteriumCollection
     */
    public function setCriteriumCollection($CriteriumCollection)
    {
        $this->CriteriumCollection = $CriteriumCollection;
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
}