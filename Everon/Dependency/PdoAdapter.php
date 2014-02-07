<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Dependency;


trait PdoAdapter
{   
    /**
     * @var \Everon\Interfaces\PdoAdapter
     */
    protected $PdoAdapter = null;


    /**
     * @return \Everon\Interfaces\PdoAdapter
     */
    public function getPdoAdapter()
    {
        return $this->PdoAdapter;
    }

    /**
     * @param \Everon\Interfaces\PdoAdapter $PdoAdapter
     */
    public function setPdoAdapter(\Everon\Interfaces\PdoAdapter $PdoAdapter)
    {
        $this->PdoAdapter = $PdoAdapter;
    }
}
