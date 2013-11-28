<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\View\Template\Compiler;

use Everon\Helper;
use Everon\View\Template\Compiler;

class E extends Compiler
{
    use Helper\String\Compiler;
    use Helper\String\EndsWith;
    
    /**
     * @inheritdoc
     */
    public function compile($scope_name, $template_content, array $data)
    {
        try {
            $this->scope_name = $scope_name;
            $curly_data = $this->arrayToValues($data);
            $curly_data = $this->arrayDotKeysFlattern($curly_data);
            $content = $this->stringCompilerRun($template_content, $curly_data);

            $data = $this->arrayDotKeysToArray(
                $this->arrayDotKeysToScope($data, $scope_name)
            );
            
            $this->data = $data;

            $tag_name = 'e';
            $pattern = "@<$tag_name(?:\s[^/]*?)?>(.*?)</$tag_name\s*>@si";
            $content = preg_replace_callback($pattern,  [$this, 'evalPhp'], $content);
            $scope = $this->getScope($data);
            return $this->runPhp($content, $scope);
        }
        catch (\Exception $e) {
            $this->getLogger()->e_error($e);
            return '';
        }
    }

    /**
     * @param $php
     * @param $scope
     * @return string
     */
    protected function runPhp($php, $scope)
    {
        $handleError = function($errno, $errstr, $errfile, $errline, array $errcontext) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }

            throw new \Everon\Exception\TemplateEvaluationError($errstr, 0, $errno, $errfile, $errline);
        };
        
        $old_error_handler = set_error_handler($handleError);
        
        $tmpphp = tmpfile();
        $content = '';
        try {
            fwrite($tmpphp, $php);

            $meta = stream_get_meta_data($tmpphp);
            $php_file = $meta['uri'];
            
            ob_start();
            extract($scope);
            include $php_file;
            $content = ob_get_contents();
        }
        catch (\Exception $e) {
            $this->getLogger()->e_error($e."\n".$php);
            $content = '';
        }
        finally {
            ob_end_clean();
            fclose($tmpphp);
            restore_error_handler($old_error_handler);
            return $content;
        }
    }
    
    /**
     * @param $data
     * @return array
     */
    protected function getScope($data)
    {
        $scope = [];
        foreach ($data as $scope_name => $values) {
            $$scope_name = new Helper\PopoProps($values);
            $scope[$scope_name] = $$scope_name;
        }

        return $scope;
    }

    /**
     * @param $code
     * @return string
     */
    protected function phpize($code)
    {
        $needs_echo = false;
        if ($code[0] === '$' || $code[0] === '"' || $code[0] === "'") {
            $needs_echo = true;
        }

        if ($needs_echo) {
            $code = "echo $code";
        }

        $s = trim(str_replace(["\n", "\r", "\r\n"], '', $code));
        if ($this->stringEndsWith($s, '}') === false && $this->stringEndsWith($s, ';') === false) {
            $code = rtrim($code, ';').";";
        }
        
        $code = "<?php $code ?>";

        return $code;
    }

    /**
     * @param $matches
     * @return string
     */
    protected function evalPhp($matches)
    {
        $code = $this->phpize($matches[1]);
        $this->getLogger()->e($code);
        return $code;
    }    
}