<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Schema;

use Everon\Domain\Interfaces\Entity;
use Everon\Interfaces\Arrayable;
use Everon\Interfaces\Immutable;

interface Table extends Arrayable, Immutable
{
    function getName();

    /**
     * @return array
     */
    function getPlaceholderForQuery();

    /**
     * @param Entity $Entity
     * @return array
     */
    function getValuesForQuery(Entity $Entity);

    /**
     * @return array
     */
    function getColumns();
    
    /**
     * @return array
     */
    function getConstraints();

    function getPk();
}
