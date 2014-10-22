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


trait Table
{
    /**
     * @var \Everon\DataMapper\Interfaces\Schema\Table
     */
    protected $Table = null;


    /**
     * @return \Everon\DataMapper\Interfaces\Schema\Table
     */
    public function getTable()
    {
        return $this->Table;
    }

    /**
     * @param \Everon\DataMapper\Interfaces\Schema\Table
     */
    public function setTable(\Everon\DataMapper\Interfaces\Schema\Table $Table)
    {
        $this->Table = $Table;
    }
}
