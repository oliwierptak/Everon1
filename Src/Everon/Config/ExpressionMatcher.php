<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Config;

use Everon\Dependency;
use Everon\Exception;
use Everon\Helper;
use Everon\Interfaces;

class ExpressionMatcher implements Interfaces\ConfigExpressionMatcher
{
    /**
     * @var \Closure
     */
    protected $Compiler = null;
    
    protected $expressions = [
        '%application.env.url%'
    ];
    

    /**
     * @param array $expressions
     * @return \Closure
     */
    protected function buildCompiler(array $expressions)
    {
        $Compiler = function(array &$data) use ($expressions) {
            if (empty($expressions) === false && empty($data) === false) {
                array_walk_recursive($data, function(&$item) use ($expressions) {
                    $item = str_replace(array_keys($expressions), array_values($expressions), $item);
                });
            }
        };
        
        return $Compiler;
    }

    /**
     * @param array $configs_data
     * @return callable
     */
    public function getCompiler(array $configs_data)
    {
        $expressions = [];
        foreach ($this->expressions as $item) {
            $tokens = explode('.', trim($item, '%'));   //eg. %application.env.url%
            list($config_name, $config_section, $config_section_variable) = $tokens;
            /**
             * @var Interfaces\ConfigLoaderItem $ConfigLoaderItem
             */
            $ConfigLoaderItem = $configs_data[$config_name];
            $data = $ConfigLoaderItem->getData();
            
            $expressions[$item] = $data[$config_section][$config_section_variable];
        }

        //todo: remove it from here, put into manager
        $expressions['%root%'] = getcwd().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
        return $this->buildCompiler($expressions);
    }

    /**
     * @param array $expressions
     */
    public function setExpressions(array $expressions)
    {
        $this->expressions = $expressions;
        $this->Compiler = null;
    }

    /**
     * @return array
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

}