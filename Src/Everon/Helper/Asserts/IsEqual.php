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

trait IsEqual
{
    /**
     * Verifies that the specified conditions are equal.
     * The assertion fails if the conditions are not equal.
     *
     * @param mixed $value1
     * @param mixed $value2
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsEqual($value1, $value2, $message='%s and %s must be equal', $exception='Asserts')
    {
        if ($value1 !== $value2) {
            $this->throwException($exception, $message, array($value1, $value2));
        }
    }
}