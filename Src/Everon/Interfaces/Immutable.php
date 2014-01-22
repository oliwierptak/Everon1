<?php
namespace Everon\Interfaces;

interface Immutable
{

    /**
     * @param $name
     * @param mixed $value
     * @throws \Exception
     * @return void
     */
    function __set($name, $value);
}
