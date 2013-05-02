<?php
namespace Everon\Helper\Asserts;

trait IsEmpty
{
    /**
     * Verifies that the specified condition is not empty.
     * The assertion fails if the condition is empty.
     *
     * @param mixed $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsEmpty($value, $message='%s must not be empty', $exception='Asserts')
    {
        if (empty($value)) {
            $this->throwException($exception, $message, $value);
        }
    }
}