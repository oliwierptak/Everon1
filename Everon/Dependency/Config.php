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


trait Config
{

    protected $Config = null;


    /**
     * @return \Everon\Interfaces\Config
     */
    public function getConfig()
    {
        return $this->Config;
    }

    public function setConfig(\Everon\Interfaces\Config $Config)
    {
        $this->Config = $Config;
    }

}
