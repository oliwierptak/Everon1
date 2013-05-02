<?php
namespace Everon\Interfaces;


interface TemplateContainer
{
    function set($name, $value);
    function get($name, $default=null);
    function setCompiledContent($data);
    function getCompiledContent();
    function getTemplateContent();
    function setTemplateContent($content);
    function toArray();
    function getData();
    function setAllIncludes(array $includes);
    function getAllIncludes();
}