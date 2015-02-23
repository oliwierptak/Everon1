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
            throw new \Everon\Exception\Domain('Invalid relation "inversed_by" for: "%s@%s" in: "%s"', [$this->getName(), $this->getRelationMapper()->getInversedBy(), $this->getOwnerEntity()->getDomainName()]);
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

        $this->getCriteriaBuilder()->where('t.'.$this->getRelationMapper()->getInversedBy(), '=', $this->getDataMapper()->getSchema()->getTableByName($table)->validateId($value));
    }
}