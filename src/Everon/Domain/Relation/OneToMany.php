<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Domain\Relation;

use Everon\Domain;

class OneToMany extends Domain\Relation implements Domain\Interfaces\Relation
{
    protected $type = self::ONE_TO_MANY;

    protected function validate()
    {
        if ($this->getRelationMapper()->getMappedBy() === null) {
            throw new \Everon\Domain\Exception('The attribute "mapped_by" is required for OneToMany relations');
        }

        if ($this->getRelationMapper()->getInversedBy() === null) {
            throw new \Everon\Domain\Exception('The attribute "inversed_by" is required for OneToMany relations');
        }
    }

    public function AAAresolveRelationsIntoData(Domain\Interfaces\Entity $Entity)
    {
        if ($this->getRelationMapper()->isVirtual()) {
            return;
        }

        $value = $Entity->getValueByName($this->getRelationMapper()->getMappedBy());
        $Column = $this->getDataMapper()->getTable()->getColumnByName($this->getRelationMapper()->getMappedBy());

        if ($Column->isPk() && $this->getOwnerEntity()->isNew() && $value === null) {
            return;
        }

        if ($Column->isNullable() && $value === null) {
            $Entity->getRelationByName($this->getName())->reset();
            $Entity->setValueByName($this->getRelationMapper()->getMappedBy(), null);
            $Entity->getRelationByName($this->getName())->reset();
            return;
        }

        $ChildEntity = $this->getDomainManager()->getRepositoryByName($this->getName())->getEntityByPropertyValue([
            $this->getRelationMapper()->getInversedBy() => $value
        ]);

        if ($ChildEntity === null) {
            $this->getOwnerEntity()->getRelationByName($this->getName())->reset();
        }
        else {
            $this->getOwnerEntity()->getRelationByName($this->getName())->setOne($ChildEntity); //update relation
            $this->getOwnerEntity()->setValueByName($this->getRelationMapper()->getInversedBy(), $ChildEntity->getValueByName($this->getRelationMapper()->getInversedBy())); //update fields represented in relations eg. user_id -> User->getId()
        }
    }
}