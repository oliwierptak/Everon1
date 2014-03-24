<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Interfaces\Dependency;

interface SchemaReader
{
    /**
     * @return \Everon\DataMapper\Interfaces\Schema\Reader
     */
    function getSchemaReader();

    /**
     * @param \Everon\DataMapper\Interfaces\Schema\Reader
     */
    function setSchemaReader(\Everon\DataMapper\Interfaces\Schema\Reader $SchemaReader);
}