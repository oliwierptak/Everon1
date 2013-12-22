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


trait FileSystem
{

    protected $FileSystem = null;


    /**
     * @return \Everon\Interfaces\FileSystem
     */
    public function getViewManager()
    {
        return $this->FileSystem;
    }

    /**
     * @param \Everon\Interfaces\FileSystem $FileSystem
     */
    public function setViewManager(\Everon\Interfaces\FileSystem $FileSystem)
    {
        $this->FileSystem = $FileSystem;
    }

}
