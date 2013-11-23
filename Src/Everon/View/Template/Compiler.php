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

use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;
use Everon\Dependency;


abstract class Compiler implements Interfaces\TemplateCompiler
{
    use Dependency\Injection\Logger;

    use Helper\Arrays;
    use Helper\IsIterable;
    use Helper\String\Compiler;

    protected $compile_errors_trace = [];
    
    protected $scope_name = null;
    protected $content = null;
    protected $data = [];
    protected $scope = [];


    /**
     * @param $scope_name
     * @param $template_content
     * @param array $data
     * @return string
     */    
    abstract public function compile($scope_name, $template_content, array $data);
    
    abstract protected function compileScope();
    
    
    protected function run($php)
    {
        //read from cache, md5 on data etc
        //if ok, include it and return result
        //else make cache +json file with variables
        //include it and return result
        //do it in view/view manager
        
        extract($this->scope);
        ob_start();
        echo $php;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;        
    }
}