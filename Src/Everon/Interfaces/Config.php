<?php
namespace Everon\Interfaces;


interface Config
{
    function getName();
    function setName($name);
    function getFilename();
    function setFilename($filename);
    function get($name, $default=null);
    /**
     * @param $where
     * @return \Everon\Interfaces\Config
     */    
    function go($where);
    function toArray();
}
