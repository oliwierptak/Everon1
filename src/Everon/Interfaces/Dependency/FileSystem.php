<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Interfaces\Dependency;

interface FileSystem
{
    /**
     * @return \Everon\Interfaces\FileSystem
     */
    function getFileSystem();

    /**
     * @param \Everon\Interfaces\FileSystem
     */
    function setFileSystem(\Everon\Interfaces\FileSystem $FileSystem);
}