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
        $tmpphp = tmpfile();
        $content = '';
        try {
            fwrite($tmpphp, $php);

            $meta = stream_get_meta_data($tmpphp);
            $php_file = $meta['uri'];
            
            ob_start();
            $this->compileScope();
            extract($this->scope);
            include $php_file;
            $content = ob_get_contents();
        }
        catch (\Exception $e) {
            $content = '';
        }
        finally {
            ob_end_clean();
            fclose($tmpphp);
            return $content;
        }

    }
}