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
    protected static $Factory = null;


    /**
     * @return \Everon\Application\Interfaces\Factory
     */
    public function getFactory()
    {
        return static::$Factory;
    }

    /**
     * @param \Everon\Application\Interfaces\Factory $Factory
     */
    public function setFactory(\Everon\Application\Interfaces\Factory $Factory)
    {
        static::$Factory = $Factory;
    }

    public function unsetFactory()
    {
        static::$Factory = null;
    }

}
