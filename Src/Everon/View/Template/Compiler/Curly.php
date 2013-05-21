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
use Everon\Interfaces;

class Curly implements Interfaces\TemplateCompiler
{
    use Helper\IsIterable;
    
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
            $value = (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
            if ($this->isIterable($value)) {
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