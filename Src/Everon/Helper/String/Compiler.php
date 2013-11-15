<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper\String;

use Everon\Helper;
use Everon\Interfaces;

/**
 * @requires Helper\isIterable
 */
trait Compiler
{
    /**
     * @param $name
     * @param array $data
     * @param array $tags
     * @return array
     */
    protected function stringCompilerGetTokens($name, array $data, array $tags)
    {
        list($opening_tag, $closing_tag) = $tags;
        $tokens = array();
        
        foreach ($data as $index => $value) {
            if (is_array($value)) {
                $tokens[$opening_tag.$name.'.'.$index.$closing_tag] = print_r($value, true);
            }
            else {
                $tokens[$opening_tag.$name.'.'.$index.$closing_tag] = $value;
            }
        }

        return $tokens;
    }
    
    protected function stringCompilerCompile($template_content, array $data)
    {
        return str_replace(array_keys($data), array_values($data), $template_content);
    }

    /**
     * @param $template_content
     * @param array $data
     * @param array $tags
     * @return mixed
     */
    protected function stringCompilerRun($template_content, array $data, array $tags=['{','}'])
    {
        $tokens = [];
        list($opening_tag, $closing_tag) = $tags;
        foreach ($data as $name => $value) {
            $value = ($value instanceof \Closure) ? $value() : $value;
            $value = (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
            if ($this->isIterable($value)) {
                $tokens = array_merge($tokens, $this->stringCompilerGetTokens($name, $value, $tags));
            }
            else {
                $tokens[$opening_tag.$name.$closing_tag] = $value;
            }
        }
        
        $template_content = $this->stringCompilerCompile($template_content, $tokens);
    
        return $template_content;
    }

}