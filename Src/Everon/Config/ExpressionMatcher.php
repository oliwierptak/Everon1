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
    protected $expressions = [
        '%application.url%'
    ];
    

    /**
     * @param array $expressions
     * @return \Closure
     */
    protected function buildCompiler(array $expressions)
    {
        $Compiler = function(array &$data) use ($expressions) {
            if (empty($expressions)) {
                return;
            }

            array_walk_recursive($data, function(&$item) use ($expressions) {
                $item = str_replace(array_keys($expressions), array_values($expressions), $item);
            });
        };
        
        return $Compiler;
    }


    /**
     * @param Interfaces\ConfigManager $Manager
     * @return \Closure
     */
    public function getCompiler(Interfaces\ConfigManager $Manager)
    {
        $expressions = [];
        foreach ($this->expressions as $item) {
            $tokens = explode('.', trim($item, '%'));
            $config_name = $tokens[0];
            $config_key = $tokens[1];

            $Config = $Manager->getConfigByName($config_name);
            $expressions[$item] = $Config->get($config_key); //todo: make deep
        }

        return $this->buildCompiler($expressions);
    }

    /**
     * @param array $expressions
     */
    public function setExpressions(array $expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * @return array
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

}