<?php
namespace Everon\Interfaces;


interface DependencyContainer
{
    function register($name, \Closure $ServiceClosure);
    function resolve($name);
    function inject($class_name, \Everon\Interfaces\Factory $Factory, $Receiver);
    function getServices();
    function getDefinitions();
    function isRegistered($name);    
}
