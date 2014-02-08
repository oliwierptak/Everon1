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

trait LastTokenToName
{
    /**
     * @param $name
     * @param string $split
     * @return string
     */
    public function stringLastTokenToName($name, $split='\\')
    {
        $tokens = explode($split, $name);
        return array_pop($tokens);
    }
}