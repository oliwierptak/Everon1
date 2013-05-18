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

trait StripNamespace
{
    /**
     * @param $namespace
     * @param string $strip
     * @return string
     */
    public function stripNamespace($namespace, $strip)
    {
        return preg_replace('/^([\\\]?)'.$strip.'([\\\]?)/i', '', $namespace);
    }
}