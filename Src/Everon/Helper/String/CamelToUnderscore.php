<?php
namespace Everon\Helper\String;

trait CamelToUnderscore
{
    /**
     * @param $string
     * @return string
     */
    public function camelToUnderscore($string)
    {
        $camelized_string_tokens = preg_split('/(?<=[^A-Z])(?=[A-Z])/', $string);
        if (count($camelized_string_tokens) > 1) {
            return implode('_', $camelized_string_tokens);
        }

        return $string;
    }
}