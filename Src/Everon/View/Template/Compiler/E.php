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
    
    /**
     * @inheritdoc
     */
    public function compile($scope_name, $template_content, array $data)
    {
        try {
            $curly_data = $this->arrayToValues($data);
            $curly_data = $this->arrayDotKeysFlattern($curly_data);
            $content = $this->stringCompilerRun($template_content, $curly_data);

            $data = $this->arrayDotKeysToArray(
                $this->arrayDotKeysToScope($data, $scope_name)
            );

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
            $this->getLogger()->e_error($e);
            $content = '';
        }
        finally {
            ob_end_clean();
            fclose($tmpphp);
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
            $$scope_name = new \stdClass();
            foreach ($values as $key => $value) {
                $$scope_name->$key = $value;
            }

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