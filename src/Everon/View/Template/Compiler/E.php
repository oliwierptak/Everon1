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

use Everon\Dependency;
use Everon\Helper;
use Everon\View\Template\Compiler;

class E extends Compiler
{
    use Dependency\Injection\FileSystem;
    
    use Helper\String\Compiler;
    use Helper\String\EndsWith;
    use Helper\RunPhp;
    
    /**
     * @inheritdoc
     */
    public function compile($scope_name, $template_content, array $data)
    {
        try {
            $this->scope_name = $scope_name;
            $data = $this->arrayDotKeysToArray(
                $this->arrayDotKeysToScope($data, $scope_name)
            );
            
            $curly_data = $this->arrayDotKeysToScope($data, $scope_name);
            $php_content = $this->stringCompilerRun($template_content, $curly_data);

            $this->data = $data;

            $tag_name = 'e';
            $pattern = "@<$tag_name(?:\s[^/]*?)?>(.*?)</$tag_name\s*>@si";
            $php_content = preg_replace_callback($pattern,  [$this, 'evalPhp'], $php_content);
            $scope_data = $this->getScope($data);
            
            $Scope = new Scope();
            $Scope->setName($scope_name);
            $Scope->setPhp($php_content);
            $Scope->setCompiled($this->runPhp($php_content, $scope_data, $this->getFileSystem()));
            $Scope->setData($scope_data);
            
            return $Scope;
        }
        catch (\Exception $e) {
            $this->getLogger()->e_error($e);
            return '';
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