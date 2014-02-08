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


trait Logger
{

    protected $Logger = null;


    /**
     * @return \Everon\Interfaces\logger
     */
    public function getLogger()
    {
        return $this->Logger;
    }

    public function setLogger(\Everon\Interfaces\Logger $Logger)
    {
        $this->Logger = $Logger;
    }

}
