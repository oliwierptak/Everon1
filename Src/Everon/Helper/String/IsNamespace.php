<?php
namespace Everon\Helper\String;

trait IsNamespace
{
    /**
     * @param $namespace
     * @param string $match
     * @return bool
     */
    public function isNamespace($namespace, $match='everon')
    {
        return substr(strtolower($namespace),0, strlen($match)) == $match;
    }
}