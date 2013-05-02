<?php
namespace Everon\Interfaces;

interface View
{
    function getOutput();
    function setOutput($Output);
    function getTemplateDirectory();
    function setTemplateDirectory($directory);
    function getTemplateFilename($filename);
    function getTemplate($name, $data);
    function set($name, $data);
    function get($name);
    function getCompilers();
    function setCompilers(array $compilers);
    function setData(array $data);
    function getData();
    function setActionTemplate($template);
    function getActionTemplate();    
}