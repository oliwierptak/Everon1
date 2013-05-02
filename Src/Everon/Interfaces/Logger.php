<?php
namespace Everon\Interfaces;

interface Logger
{
    function setLogDirectory($directory);
    function getLogDirectory();
    function setLogFiles(array $files);
    function getLogFiles();
    function warn($message);
    function error($message);
    function debug($message);
    function trace($message);
}
