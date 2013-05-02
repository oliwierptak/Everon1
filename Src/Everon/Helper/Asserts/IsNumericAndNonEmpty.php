<?php
namespace Everon\Helper\Asserts;

trait IsNumericAndNonEmpty
{
    /**
     * Verifies that the specified condition is numeric and non empty.
     * The assertion fails if the condition is not numeric or empty numeric.
     *
     * @param mixed $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsNumericAndNonEmpty($value, $message='%s must be a number and not empty', $exception='Asserts')
    {
        if (!is_numeric($value) || strlen(strval($value)) < 1) {
            $this->throwException($exception, $message, $value);
        }
    }
}