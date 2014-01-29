<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Dependency;


trait SchemaReader
{
    protected $SchemaReader = null;


    /**
     * @return \Everon\DataMapper\Interfaces\Schema\Reader
     */
    public function getSchemaReader()
    {
        return $this->SchemaReader;
    }

    /**
     * @param \Everon\DataMapper\Interfaces\Schema\Reader
     */
    public function setSchemaReader(\Everon\DataMapper\Interfaces\Schema\Reader $SchemaReader)
    {
        $this->SchemaReader = $SchemaReader;
    }
}
