<?php
namespace Everon\Helper\String;

trait FixPath
{
    /**
     * @param $path
     * @return string
     */
    public function fixPath($path)
    {
        return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    }
}