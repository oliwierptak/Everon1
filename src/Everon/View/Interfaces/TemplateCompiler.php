<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Interfaces;

interface TemplateCompiler
{
    /**
     * @param TemplateCompilerContext $Context
     * @internal param \Everon\View\Interfaces\View $View
     * @internal param $scope_name
     * @internal param $template_content
     * @internal param array $data
     */    
    function compile(TemplateCompilerContext $Context);
}