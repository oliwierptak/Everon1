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

class ExpressionMatcher implements Interfaces\ExpressionMatcher
{
    use Helper\Arrays;
    use Helper\IsIterable;

    protected $expressions = [];
    protected $values = [];


    /**
     * @param array $configs_data
     * @param array $custom_expressions
     * @return callable
     */
    public function compile(array &$configs_data, array $custom_expressions=[])
    {
        $this->expressions = [];
        $this->values = [];

        $makeValues = function($config_name, &$data) {
            foreach ($data as $name => $values) {
                if ($this->isIterable($values)) {
                    foreach ($values as $key => $key_value) {
                        if ($this->isIterable($key_value)) {
                            foreach ($key_value as $vvkey => $vvkey_value) {
                                $this->values["%${config_name}.${name}.${key}.${vvkey}%"] = $key_value[$vvkey];    //xxx -.-
                            }
                        }
                        else {
                            $this->values["%${config_name}.${name}.${key}%"] = $values[$key];
                        }
                    }
                }
                else {
                    $this->values["%${config_name}.${name}%"] = $values;
                }
            }
        };

        foreach ($configs_data as $config_name => $one_config_data) {
            $makeValues($config_name, $one_config_data['data']);
        }

        $this->values = array_merge($this->values, $custom_expressions);
        foreach ($this->values as $name => $value) {
            $this->values[$name] = str_replace(array_keys($this->values), array_values($this->values), $value);
        }

        foreach ($configs_data as $config_name => $one_config_data) {
            foreach ($one_config_data['data'] as $section_name => $section_data) {
                array_walk_recursive($configs_data[$config_name]['data'][$section_name], function(&$item) {
                    $item = str_replace(array_keys($this->values), array_values($this->values), $item);
                });
            }
        }
    }
}