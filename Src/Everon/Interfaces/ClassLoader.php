<?php
namespace Everon\Interfaces;

interface ClassLoader
{
    function register();
    function unRegister();
    function add($namespace, $directory);
    function load($class_name);    
}
