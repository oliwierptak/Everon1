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
        try {
            $class = $exception_class;
            if (class_exists($class, true) === false) {
                $exception_class = 'Everon\Exception\Asserts';
            }
        }
        catch (\Exception $e) {
            try {
                $class = "Everon\Exception\\${exception_class}";
                if (class_exists($class, true) === false) {
                    $exception_class = 'Everon\Exception\Asserts';
                }
                else {
                    $exception_class = "Everon\Exception\\${exception_class}";
                }
            }
            catch (\Exception $e) {
                $exception_class = 'Everon\Exception\Asserts';
            }
        }    

        throw new $exception_class($message, $value);
    }
}