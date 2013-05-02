<?php
namespace Everon\Helper\Asserts;

trait IsArrayAndNotEmpty
{
    /**
     * Verifies that the specified condition is not empty array.
     * The assertion fails if the condition is not array or it's an empty array.
     *
     * @param mixed $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsArrayAndNotEmpty($value, $message='%s must not be an empty Array', $exception='Asserts')
    {
        if (!is_array($value) || (is_array($value) && count($value) <= 0)) {
            $this->throwException($exception, $message, $value);
        }
    }
}