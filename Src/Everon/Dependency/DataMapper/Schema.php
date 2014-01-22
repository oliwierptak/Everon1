<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency\DataMapper;


trait Schema
{
    protected $Schema = null;


    /**
     * @return \Everon\DataMapper\Interfaces\Schema
     */
    public function getSchema()
    {
        return $this->Schema;
    }

    /**
     * @param \Everon\DataMapper\Interfaces\Schema
     */
    public function setView(\Everon\DataMapper\Interfaces\Schema $Schema)
    {
        $this->Schema = $Schema;
    }
}
