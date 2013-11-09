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

use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;
use Everon\Dependency;

class Curly implements Interfaces\TemplateCompiler
{
    use Dependency\Injection\Logger;
    
    use Helper\IsIterable;
    use Helper\String\Compiler;

    protected $php_keyword = [
        'foreach',
        'eval',
        'for',
        'if',
        'switch',
        'unset',
        'new',
        'echo',
        'require',
        'include',
        'require_once',
        'include_once',
        'array',
        'try',
        'catch',
        'final',
        'throw',
        'else',
        'function',
        'isset',
        'unset',
        'print',
        'while',
        'class',
        'list',
    ];
    
    protected $compile_erros = [];

    /**
     * @param $template_content
     * @param array $data
     * @return string
     */
    public function compile($template_content, array $data)
    {
        $content = $this->stringCompilerCompile($template_content, $data, ['{','}']);
        return $this->compileCurly($content);
    }

    protected function compileCurly($template_content)
    {
        $this->compile_erros = [];
        $tag_name = 'e';
        $pattern = "@<$tag_name(?:\s[^/]*?)?>(.*?)</$tag_name\s*>@s";
        $content = preg_replace_callback($pattern,  [$this, 'evalPhp'], $template_content);
        
        if ($this->compile_erros) {
            $this->getLogger()->template_compiler(
                implode("\n", $this->compile_erros)
            );
        }
        
        return $content;
    }

    protected function phpizer($code) 
    {
        $code = trim($code);
        
        if ($code[0] === '$') {
            $needs_echo = false;
        }
        else {
            $keywords = implode('|', $this->php_keyword);
            $needs_echo = preg_match('@^('.$keywords.')@i', $code, $m) === 0;
        }

        if ($needs_echo) {
            return "echo $code";
        }

        return $code;
    }

    protected function evalPhp($matches) 
    {
        ob_start();
        try {
            $code = $this->phpizer($matches[1]);
            $code = rtrim($code, ';').";";
            $this->getLogger()->template_eval($code);
            $e = @eval($code);
            if ($e === false) {
                debug_print_backtrace();
            }
        }
        catch (\Exception $e) {
        }

        $output = ob_get_contents();
        ob_end_clean();
        
        if ($e === false) {
            $this->compile_erros[] = $output;
            $output = '';
        }

        return $output;
    }
}