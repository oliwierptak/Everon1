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

trait IsArrayKey
{
    /**
     * Verifies that the specified condition is a key in the array.
     * The assertion fails if the key is not in the array or the the $array_to_check is not an array.
     *
     * @param mixed $key
     * @param mixed $array_to_check
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsArrayKey($key, $array_to_check, $message='%s does not exist in array as a key', $exception='Asserts')
    {
        if(!is_array($array_to_check) || (is_array($array_to_check) && !array_key_exists($key, $array_to_check))) {
            $this->throwException($exception, $message, $key);
        }
    }
}