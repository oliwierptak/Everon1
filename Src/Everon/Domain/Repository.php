<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain;

use Everon\Domain\Interfaces;
use Everon\Interfaces\DataMapper;

abstract class Repository implements Interfaces\Repository
{
    /**
     * @var DataMapper
     */
    protected $Mapper = null;
    
    protected $name = null;
    
    
    public function __construct($name, DataMapper $Mapper)
    {
        $this->name = $name;
        $this->Mapper = $Mapper;
    }

    /**
     * @inheritdoc
     */
    public function getMapper()
    {
        return $this->Mapper;
    }

    /**
     * @inheritdoc
     */
    public function setMapper(DataMapper $Mapper)
    {
        $this->Mapper = $Mapper;
    }
    
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function persist(Interfaces\Entity $Entity)
    {
        if ($Entity->isNew()) {
            $this->getMapper()->add($Entity);
        }
        else {
            $this->getMapper()->save($Entity);
        }

        $Entity->persist();
    }
}