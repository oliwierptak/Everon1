<?php
namespace Everon\Interfaces;

interface Logger
{
    function setLogDirectory($directory);
    function getLogDirectory();
    function setLogFiles(array $files);
    function getLogFiles();
    function warn($message, $parameters=[]);
    function error($message, $parameters=[]);
    function debug($message, $parameters=[]);
    function trace($message, $parameters=[]);
}
