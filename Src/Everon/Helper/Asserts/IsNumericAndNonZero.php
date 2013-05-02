<?php
namespace Everon\Helper\Asserts;

trait IsNumericAndNonZero
{
    /**
     * Verifies that the specified condition is numeric and non zero.
     * The assertion fails if the condition is not numeric or numeric less or equal zero.
     *
     * @param integer $value
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsNumericAndNonZero($value, $message='%s must be a number and not 0', $exception='Asserts')
    {
        if (!is_numeric($value) || intval($value) <= 0) {
            $this->throwException($exception, $message, $value);
        }
    }
}