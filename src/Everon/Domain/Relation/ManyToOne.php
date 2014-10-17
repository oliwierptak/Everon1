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

class ManyToOne extends Domain\Relation implements Domain\Interfaces\Relation
{
    protected $type = self::MANY_TO_ONE;

    protected function validate()
    {
        if ($this->getRelationMapper()->getMappedBy() === null) {
            throw new Domain\Exception('The attribute "mapped_by" is required for ManyToOne relations');
        }

        if ($this->getRelationMapper()->getInversedBy() === null) {
            throw new Domain\Exception('The attribute "inversed_by" is required for ManyToOne relations');
        }
    }

    protected function setupRelation()
    {
        $domain_name = $this->getOwnerEntity()->getDomainName();
        $Repository = $this->getDomainManager()->getRepositoryByName($domain_name);
        
        $inversed_table = $this->getDataMapper()->getTable()->getFullName();

        if (array_key_exists($this->getRelationMapper()->getInversedBy(), $this->getDataMapper()->getTable()->getForeignKeys()) === false) {
            throw new \Everon\Exception\Domain('Invalid relation "inversed_by" for: "%s@%s', [$this->getName(), $this->getRelationMapper()->getInversedBy()]);
        }

        /**
         * @var \Everon\DataMapper\Interfaces\Schema\ForeignKey $ForeignKey
         */
        $ForeignKey = $this->getDataMapper()->getTable()->getForeignKeys()[$this->getRelationMapper()->getInversedBy()];
        $table = $ForeignKey->getFullTableName();
        $owner_column = $this->getRelationMapper()->getMappedBy();

        $value = $this->getOwnerEntity()->getValueByName($owner_column);
        $Column = $Repository->getMapper()->getSchema()->getTableByName($inversed_table)->getColumnByName($this->getRelationMapper()->getInversedBy()); //todo DataMapper leak
        if ($Column->isNullable() && $value === null) {
            $this->loaded = true; //xxx the value is null stop loading
            return;
        }

        $this->getCriteria()->where([
            't.'.$this->getRelationMapper()->getInversedBy() => $this->getDataMapper()->getSchema()->getTableByName($table)->validateId($value)
        ]);
    }

    public function AAAresolveRelationsIntoData(Domain\Interfaces\Entity $Entity)
    {
        die('no no');
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