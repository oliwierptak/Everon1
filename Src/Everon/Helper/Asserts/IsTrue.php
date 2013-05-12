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

trait IsTrue
{
    /**
     * Verifies that the specified condition is true.
     * The assertion fails if the condition is false.
     *
     * @param bool $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsTrue($value, $message='%s must be True', $exception='Asserts')
    {
        if (!is_bool($value) || $value !== true) {
            $this->throwException($exception, $message, $value);
        }
    }
}