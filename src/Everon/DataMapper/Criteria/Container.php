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

use Everon\Helper;
use Everon\DataMapper\Interfaces;

class Container implements Interfaces\Criteria\Container
{
    /**
     * @var Interfaces\Criteria
     */
    protected $Criteria = null;

    /**
     * @var string
     */
    protected $glue = null;

    /**
     * @param Interfaces\Criteria $Criteria
     * @param $glue
     */
    public function __construct(Interfaces\Criteria $Criteria, $glue)
    {
        $this->Criteria = $Criteria;
        $this->glue = $glue;
    }

    /**
     * @return Interfaces\Criteria
     */
    public function getCriteria()
    {
        return $this->Criteria;
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
}