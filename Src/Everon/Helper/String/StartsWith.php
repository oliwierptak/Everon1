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

trait StartsWith
{
    /**
     * @param $string
     * @param $start
     * @return bool
     */
    public function stringStartsWith($string, $start)
    {
        return mb_strpos($string, $start) === 0;
    }
}