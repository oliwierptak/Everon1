<?php
namespace Everon\Interfaces;

use Everon\Interfaces;

interface ConfigExpressionMatcher
{
    /**
     * @param Interfaces\ConfigManager $Manager
     * @return callable
     */
    function getCompiler(Interfaces\ConfigManager $Manager);
    function setExpressions(array $expressions);
    function getExpressions();
}