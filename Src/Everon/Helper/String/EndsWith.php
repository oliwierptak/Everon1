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

trait EndsWith
{
    /**
     * @param $string
     * @param $end
     * @return bool
     */
    public function stringEndsWith($string, $end)
    {
        return substr_compare($string, $end, -strlen($end), strlen($end)) === 0;
    }
}