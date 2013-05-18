<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper\String;

trait IsNamespace
{
    /**
     * @param $namespace
     * @param string $match
     * @return bool
     */
    public function isNamespace($namespace, $match)
    {
        return strcasecmp(substr($namespace,0, strlen($match)), $match) == 0;
    }
}