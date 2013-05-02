<?php
namespace Everon\Helper;

use Everon\Exception;

trait Regex
{

    protected function regexCompleteAndValidate($name, $pattern)
    {
        $pattern = '@^'.$pattern.'$@';
        $this->regexValidate($name, $pattern);

        return $pattern;
    }

    /**
     * @param $name
     * @param $regex
     * @throws \Everon\Exception\Helper
     */
    protected function regexValidate($name, $regex)
    {
        $match = (@preg_match($regex, null));
        if ($match === false) {
            throw new Exception\Helper('Invalid regex for: "%s"', $name);
        }
    }

}