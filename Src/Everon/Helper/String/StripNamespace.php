<?php
namespace Everon\Helper\String;

trait StripNamespace
{
    /**
     * @param $namespace
     * @param string $strip
     * @return string
     */
    public function stripNamespace($namespace, $strip='everon')
    {
        return preg_replace('/^([\\\]?)'.$strip.'([\\\]?)/i', '', $namespace);
    }
}