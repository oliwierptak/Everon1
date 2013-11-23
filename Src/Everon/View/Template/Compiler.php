<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Template;

use Everon\Helper;
use Everon\Interfaces;
use Everon\Dependency;


abstract class Compiler implements Interfaces\TemplateCompiler
{
    use Dependency\Injection\Logger;

    use Helper\Arrays;
    use Helper\IsIterable;
    

    /**
     * @param $scope_name
     * @param $template_content
     * @param array $data
     * @return string
     */    
    abstract public function compile($scope_name, $template_content, array $data);
}