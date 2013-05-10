<?php
namespace Everon\Interfaces;

interface ClassMap
{
    function addToMap($class, $file);
    function loadMap();
    function saveMap();
    function getFilenameFromMap($class);
}
