<?php
namespace Everon\View\Template\Compiler;

use Everon\Interfaces;

class Curly implements Interfaces\TemplateCompiler
{

    /**
     * @param $name
     * @param array $data
     * @return array
     */
    protected function getTokens($name, array $data)
    {
        $tokens = array();
        foreach ($data as $index => $value) {
            if (is_array($value)) {
                $tokens['{'.$name.'.'.$index.'}'] = print_r($value, true);
            }
            else {
                $tokens['{'.$name.'.'.$index.'}'] = $value;
            }
        }

        return $tokens;
    }

    /**
     * @param $template_content
     * @param array $data
     * @return string
     */
    public function compile($template_content, array $data)
    {
        foreach ($data as $name => $value) {
            $value = ($value instanceof \Closure) ? $value() : $value;
            if (($value instanceof Interfaces\Arrayable) || is_array($value)) { //todo: replace with is_iterrable() or something
                $value = is_array($value) ? $value : $value->toArray();
                $tokens = $this->getTokens($name, $value);
                $template_content = str_replace(array_keys($tokens), array_values($tokens), $template_content);
            }
            else {
                $template_content = str_replace('{'.$name.'}', $value, $template_content);
            }
        }
    
        return $template_content;
    }

}