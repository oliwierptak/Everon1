<?php
namespace Everon\Interfaces;

interface CLassLoader
{
    function register();
    function unRegister();
    function add($namespace, $directory);
    function load($class_name);    
}
