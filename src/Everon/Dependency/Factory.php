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


trait Factory
{

    /**
     * @var \Everon\Application\Interfaces\Factory
     */
    protected $Factory = null;


    /**
     * @return \Everon\Application\Interfaces\Factory
     */
    public function getFactory()
    {
        return $this->Factory;
    }

    /**
     * @param \Everon\Application\Interfaces\Factory $Factory
     */
    public function setFactory(\Everon\Application\Interfaces\Factory $Factory)
    {
        $this->Factory = $Factory;
    }

}
