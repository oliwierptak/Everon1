<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Dependency;


trait DomainMapper
{
    /**
     * @var \Everon\Domain\Interfaces\Mapper
     */
    protected $DomainMapper = null;


    /**
     * @return \Everon\Domain\Interfaces\Mapper
     */
    public function getDomainMapper()
    {
        return $this->DomainMapper;
    }

    /**
     * @param \Everon\Domain\Interfaces\Mapper
     */
    public function setDomainMapper(\Everon\Domain\Interfaces\Mapper $Mapper)
    {
        $this->DomainMapper = $Mapper;
    }

}
