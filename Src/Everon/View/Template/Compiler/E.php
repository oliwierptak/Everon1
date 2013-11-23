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

use Everon\View\Template\Compiler;

class E extends Compiler
{

    /**
     * @inheritdoc
     */
    public function compile($scope_name, $template_content, array $data)
    {
        $curly_data = $this->arrayToValues($data);
        $curly_data = $this->arrayDotKeysFlattern($curly_data);
        $template_content = $this->stringCompilerRun($template_content, $curly_data);

        $this->content = $template_content;
        $this->scope_name = $scope_name;

        $this->data = $this->arrayDotKeysToArray(
            $this->arrayDotKeysToScope($data, $scope_name)
        );

        $tag_name = 'e';
        $pattern = "@<$tag_name(?:\s[^/]*?)?>(.*?)</$tag_name\s*>@si";
        $this->content = preg_replace_callback($pattern,  [$this, 'evalPhp'], $this->content);
        $this->content = $this->run($this->content);

        return $this->content;        
    }

    protected function compileScope()
    {
        $this->scope = [];
        foreach ($this->data as $scope_name => $values) {
            $$scope_name = new \stdClass();
            foreach ($values as $key => $value) {
                $$scope_name->$key = $value;
            }

            $this->scope[$scope_name] = $$scope_name;
        }
    }


    /**
     * @param $code
     * @return string
     */
    protected function phpize($code)
    {
        $needs_echo = false;
        if ($code[0] === '$') {
            $needs_echo = true;
        }

        if ($needs_echo) {
            $code = "echo $code";
        }

        $code = rtrim($code, ';').";";
        $code = "<?php $code ?>";

        return $code;
    }

    protected function evalPhp($matches)
    {
        try {
            $code = $this->phpize($matches[1]);
            $this->getLogger()->e($code);
            return $code;
        }
        catch (\Exception $e) {
            $this->getLogger()->e_error($e);
            return '';
        }
    }    
}