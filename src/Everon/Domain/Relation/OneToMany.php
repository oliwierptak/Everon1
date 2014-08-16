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

abstract class OneToMany extends \Everon\Domain\Relation implements \Everon\Domain\Interfaces\Relation
{
    protected $type = self::ONE_TO_MANY;

    protected function validate()
    {
        if ($this->getRelationMapper()->getMappedBy() === null) {
            throw new \Everon\Domain\Exception('The attribute "mapped_by" is required for OneToOne relations');
        }

        if ($this->getRelationMapper()->getInversedBy() === null) {
            throw new \Everon\Domain\Exception('The attribute "inversed_by" is required for OneToMany relations');
        }
    }
}