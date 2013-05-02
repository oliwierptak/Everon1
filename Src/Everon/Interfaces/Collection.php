<?php
namespace Everon\Interfaces;

interface Collection
{
    function has($name);
    function remove($name);
    function set($name, $value);
    function get($name);
}