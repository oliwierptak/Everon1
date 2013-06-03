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

trait CamelToUnderscore
{
    /**
     * @param $string
     * @return string
     */
    public function stringCamelToUnderscore($string)
    {
        $camelized_string_tokens = preg_split('/(?<=[^A-Z])(?=[A-Z])/', $string);
        if (count($camelized_string_tokens) > 1) {
            return implode('_', $camelized_string_tokens);
        }

        return $string;
    }
}