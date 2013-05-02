<?php
namespace Everon\Helper\Asserts;

trait IsInstance
{
    /**
     * Verifies that the specified conditions are of the same instance.
     * The assertion fails if they are not.
     *
     * @param mixed $class Concreet class to check
     * @param string $instance Instance name
     * @param string $message
     * @param string $exception
     * @throws \Everon\Exception\Asserts
     */
    public function assertIsInstance($class, $instance, $message='%s and %s are not the same instance', $exception='Asserts')
    {
        $is_instance = (get_class($class) === $instance);
        if (!$is_instance) {
            $this->throwException($exception, $message, array(get_class($class), $instance));
        }
    }
}