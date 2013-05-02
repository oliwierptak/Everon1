<?php
namespace Everon\Helper\String;

trait SetFirstAndLastCharacter
{
    /**
     * @param $haystack
     * @param $replacement
     * @return string
     */
    public function setFirstAndLastCharacter($haystack, $replacement)
    {
        $haystack = ltrim($haystack, $replacement);
        $haystack = rtrim($haystack, $replacement);

        return $replacement.$haystack.$replacement;
    }
}