<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces;

interface Handler extends \Everon\Domain\Interfaces\Dependency\DomainMapper, \Everon\DataMapper\Interfaces\Dependency\ConnectionManager
{
    /**
     * @return Schema
     */
    function getSchema();

    /**
     * @param Schema $Schema
     */
    function setSchema(Schema $Schema);
}
