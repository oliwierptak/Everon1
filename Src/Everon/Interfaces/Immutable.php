<?php
namespace Everon\Interfaces;

interface Immutable
{

    /**
     * @throws \Exception
     * @return void
     */
    function __set();
}
