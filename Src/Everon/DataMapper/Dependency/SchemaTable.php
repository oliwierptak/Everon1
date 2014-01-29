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


trait SchemaTable
{
    protected $SchemaTable = null;


    /**
     * @return \Everon\DataMapper\Interfaces\Schema\Table
     */
    public function getSchemaTable()
    {
        return $this->SchemaTable;
    }

    /**
     * @param \Everon\DataMapper\Interfaces\Schema\Table
     */
    public function setSchemaTable(\Everon\DataMapper\Interfaces\Schema\Table $Table)
    {
        $this->SchemaTable = $Table;
    }
}
