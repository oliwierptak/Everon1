<?php
namespace Everon\Helper;

trait Asserts
{
    /**
     * @param $message
     * @param $parameters
     * @return string
     */
    protected function formatExceptionParams($message, $parameters)
    {
        if (trim($message) == '' || is_null($parameters)) {
            return $message;
        }

        if (!is_array($parameters)) {
            $parameters = array($parameters);
        }

        return vsprintf($message, $parameters);
    }

    /**
     * @param string $exception_class
     * @param string $message
     * @param mixed $value
     * @throws \Exception
     */
    protected function throwException($exception_class, $message, $value)
    {
        if (!class_exists($exception_class)) {
            $exception_class = '\\Everon\\Exception\\'.$exception_class;
        }
        throw new $exception_class($message, $value);
    }
}