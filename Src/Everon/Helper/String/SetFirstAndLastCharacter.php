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

trait SetFirstAndLastCharacter
{
    /**
     * @param $haystack
     * @param $replacement
     * @return string
     */
    public function setFirstAndLastCharacter($haystack, $replacement)
    {
        $haystack = ltrim($haystack, $replacement);
        $haystack = rtrim($haystack, $replacement);

        return $replacement.$haystack.$replacement;
    }
}