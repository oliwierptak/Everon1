<?php
namespace Everon\Helper\Asserts;

trait IsNull
{
    /**
     * Verifies that the specified condition is not null.
     * The assertion fails if the condition is null.
     *
     * @param mixed $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsNull($value, $message='Unexpected null value: %s', $exception='Asserts')
    {
        if (is_null($value)) {
            $this->throwException($exception, $message, $value);
        }
    }
}