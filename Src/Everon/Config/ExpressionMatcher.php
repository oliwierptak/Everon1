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
    protected $expressions = ['%application.env.url%'=>'']; //defaults
    protected $values = [];
    

    /**
     * @param array $expressions
     * @return \Closure
     */
    protected function buildCompiler()
    {
        $Compiler = function(array &$data) {
            if (empty($this->values) === false && empty($data) === false) {
                array_walk_recursive($data, function(&$item) {
                    $item = str_replace(array_keys($this->values), array_values($this->values), $item);
                });
            }
        };

        return $Compiler;
    }

    /**
     * @param array $configs_data
     * @param array $custom_expressions
     * @return callable
     */
    public function createCompiler(array $configs_data, array $custom_expressions=[])
    {
        $this->tokenizeExpressions($configs_data);
        foreach ($this->expressions as $item) {
            $tokens = explode('.', trim($item, '%'));   //eg. %application.env.url%
            if (count($tokens) < 3) {
                continue;
            }
            
            list($config_name, $config_section, $config_section_variable) = $tokens;
            $data = $configs_data[$config_name];
            $this->values[$item] = $data[$config_section][$config_section_variable];
        }

        //todo: remove it from here, put into manager and read the value from Environment 
        //$values['%application.env.root%'] = getcwd().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
        
        //to update self referencing, eg.
        //'%application.assets.themes%' => string (34) "%application.env.url_statc%themes/"
        $Compiler = $this->buildCompiler();
        $Compiler($this->values); 
        
        return $this->buildCompiler();
    }
    
    public function tokenizeExpressions(array $data)
    {
        /**
         * @var  Loader\Item $config_data
         */
        foreach ($data as $config_name => $config_data) {
            $Callback = function($item) {
                preg_match('(%([^\%]*)\.([^\%]*)\.([^\%]*)%)', $item, $matches); //%application.assets.themes%
                if (empty($matches) === false) {
                    $this->expressions[$matches[0]] = $matches[0];
                }
            };
            array_walk_recursive($config_data, $Callback);
        }

        $this->expressions = array_keys($this->expressions);
    }
}