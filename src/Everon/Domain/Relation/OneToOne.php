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

class OneToOne extends Domain\Relation implements Domain\Interfaces\Relation
{
    protected $type = self::ONE_TO_ONE;

    protected function validate()
    {
        if ($this->getRelationMapper()->getMappedBy() === null && $this->getRelationMapper()->getInversedBy() === null) {
            throw new \Everon\Domain\Exception('The attribute "mapped_by" or "inversed_by" is required for OneToOne relations for: "%s"', $this->getOwnerEntity()->getDomainName());
        }

        if ($this->getRelationMapper()->isOwningSide()) {
            if ($this->getRelationMapper()->getMappedBy() === null) {
                throw new \Everon\Domain\Exception('The attribute "mapped_by" is required for owning side of OneToOne relation for: "%s"', $this->getOwnerEntity()->getDomainName());    
            }

            if ($this->getRelationMapper()->getInversedBy() !== null) {
                throw new \Everon\Domain\Exception('The attribute "inversed_by" is not allowed for owning side of OneToOne relation for: "%s"', $this->getOwnerEntity()->getDomainName());
            }
        }

        if ($this->getRelationMapper()->isOwningSide() === false) {
            if ($this->getRelationMapper()->getMappedBy() !== null) {
                throw new \Everon\Domain\Exception('The attribute "mapped_by" is not allowed for inverse side of OneToOne relation for: "%s"', $this->getOwnerEntity()->getDomainName());
            }
            
            if ($this->getRelationMapper()->getInversedBy() === null) {
                throw new \Everon\Domain\Exception('The attribute "inversed_by" is required for inverse side of OneToOne relation for: "%s"', $this->getOwnerEntity()->getDomainName());
            }
        }
    }

    protected function setupRelation()
    {
        $domain_name = $this->getOwnerEntity()->getDomainName();
        $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);

        /**
         * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey
         */
        if ($this->getRelationMapper()->isOwningSide() === false) {
            if (array_key_exists($this->getRelationMapper()->getInversedBy(), $this->getDataMapper()->getTable()->getForeignKeys()) === false) {
                throw new \Everon\Exception\Domain('Invalid OneToOne owning relation property "inversed_by" for: "%s@%s', [$this->getName(), $this->getRelationMapper()->getInversedBy()]);
            }

            $ForeignKey = $this->getDataMapper()->getTable()->getForeignKeys()[$this->getRelationMapper()->getInversedBy()];
            //$table = $ForeignKey->getFullTableName();
            $target_column = $this->getRelationMapper()->getColumn() ?: $ForeignKey->getColumnName();

            $value = $this->getOwnerEntity()->getValueByName($this->getRelationMapper()->getColumn());
            $Column = $this->getDataMapper()->getTable()->getColumnByName($this->getRelationMapper()->getInversedBy()); //todo DataMapper leak
        }
        else {
            if (array_key_exists($this->getRelationMapper()->getMappedBy(), $Repository->getMapper()->getTable()->getForeignKeys()) === false) {
                throw new \Everon\Exception\Domain('Invalid OneToOne inversed relation property "mapped_by" for: "%s@%s', [$this->getName(), $this->getRelationMapper()->getMappedBy()]);
            }

            $ForeignKey = $Repository->getMapper()->getTable()->getForeignKeys()[$this->getRelationMapper()->getMappedBy()];
            //$table = $ForeignKey->getForeignFullTableName();
            $target_column = $this->getRelationMapper()->getColumn() ?: $ForeignKey->getForeignColumnName();

            $value = $this->getOwnerEntity()->getValueByName($this->getRelationMapper()->getMappedBy());
            $Column = $Repository->getMapper()->getTable()->getColumnByName($this->getRelationMapper()->getMappedBy()); //todo DataMapper leak
        }


        $Column->validateColumnValue($value);
        if ($Column->isNullable() && $value === null) {
            $this->loaded = true; //xxx the value is null stop loading
            return;
        }

        $this->getCriteria()->where([
            't.'.$target_column => $value
        ]);
    }

    public function AAAresolveRelationsIntoData(Domain\Interfaces\Entity $Entity)
    {
        if ($this->getRelationMapper()->isVirtual()) {
            return;
        }

        $value = $this->getOwnerEntity()->getValueByName($this->getRelationMapper()->getMappedBy());
        $Column = $this->getDataMapper()->getTable()->getColumnByName($this->getRelationMapper()->getMappedBy());

        if ($Column->isPk() && $this->getOwnerEntity()->isNew() && $value === null) {
            return;
        }

        if ($Column->isNullable() && $value === null) {
            $this->getOwnerEntity()->getRelationByName($this->getName())->reset();
            $this->getOwnerEntity()->setValueByName($this->getRelationMapper()->getMappedBy(), null);
            $this->getOwnerEntity()->getRelationByName($this->getName())->reset();
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