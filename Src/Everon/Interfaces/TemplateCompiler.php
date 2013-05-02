<?php
namespace Everon\Interfaces;


interface TemplateCompiler
{
    function compile($template_content, array $data);
}