<?php
namespace Everon\Model;

abstract class Manager implements \Everon\Interfaces\ModelManager
{

    use \Everon\Dependency\Config;

    abstract public function init();

}