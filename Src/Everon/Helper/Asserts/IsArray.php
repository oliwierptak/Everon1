<?php
namespace Everon\Helper\Asserts;

trait IsArray
{
    /**
     * Verifies that the specified condition is array.
     * The assertion fails if the condition is not array.
     *
     * @param mixed $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsArray($value, $message='%s must be an Array', $exception='Asserts')
    {
        if (!is_array($value)) {
            $this->throwException($exception, $message, $value);
        }
    }
}