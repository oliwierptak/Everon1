<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest\Resource;

use Everon\Helper;
use Everon\Domain\Interfaces\Entity;

abstract class Domain extends \Everon\Rest\Resource implements \Everon\Rest\Interfaces\ResourceDomain
{
    /**
     * @var Entity
     */
    protected $ResourceEntity = null;

    /**
     * @var \Everon\Interfaces\Collection
     */
    protected $RelationCollection = null;

    protected $relation_definition = [];


    public function __construct($name, $version, Entity $Entity)
    {
        parent::__construct($name, $version);
        $this->ResourceEntity = $Entity;
        $this->RelationCollection = new Helper\Collection([]);
    }
    
    protected function init()
    {
        if ($this->data === null) {
            $this->data = $this->ResourceEntity->toArray();
        }
    }
    

    /**
     * @inheritdoc
     */
    public function getResourceEntity()
    {
        return $this->ResourceEntity;
    }

    /**
     * @return array
     */
    public function getRelationDefinition()
    {
        return $this->relation_definition;
    }

    /**
     * @param \Everon\Interfaces\Collection $ResourceCollection
     */
    public function setRelationCollection(\Everon\Interfaces\Collection $ResourceCollection)
    {
        $this->RelationCollection = $ResourceCollection;
    }

    /**
     * @return \Everon\Interfaces\Collection
     */
    public function getRelationCollection()
    {
        return $this->RelationCollection;
    }
}
