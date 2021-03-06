<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;

trait Exceptions
{
    /**
     * @param $message
     * @param $parameters
     * @return string
     */
    protected function formatExceptionParams($message, $parameters)
    {
        if (trim($message) === '' || is_null($parameters)) {
            return $message;
        }

        if (is_array($parameters) === false) {
            $parameters = array($parameters);
        }

        return @vsprintf($message, $parameters);
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
            try {
                if (class_exists($exception_class, true) === false) {
                    $class = "Everon\\Exception\\${exception_class}";
                    if (class_exists($class, true)) {
                        $exception_class = $class;
                    }
                }
            }
            catch (\RuntimeException $e) {
                $class = "Everon\\Exception\\${exception_class}";
                if (class_exists($class, true)) {
                    $exception_class = $class;
                }
            }
        }
        catch (\RuntimeException $e) {
            $class = "Everon\\Exception\\${exception_class}";
            if (class_exists($class, true) === false) {
                $exception_class = 'Everon\Exception\Asserts';
            }
            else {
                $exception_class = $class;
            }
        }

        throw new $exception_class($message, $value);
    }
}