<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper\Asserts;

trait IsInArray
{
    /**
     * Verifies that the specified condition is in the array.
     * The assertion fails if the condition is not in the array or the the $haystack is not an array.
     *
     * @param mixed $needle
     * @param mixed $haystack
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsInArray($needle, $haystack, $message='%s must be in Array', $exception='Asserts')
    {
        if (!is_array($haystack) || !in_array($needle, $haystack)) {
            $this->throwException($exception, $message, $needle);
        }
    }
}