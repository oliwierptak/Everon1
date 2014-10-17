<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Helper;

trait Arrays
{
    /**
     * @param $value
     * @return mixed
     */
    protected function arrayToValues($value)
    {
        $value = ($value instanceof \Closure) ? $value() : $value;
        $value = (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
        
        if ($this->isIterable($value)) {
            foreach ($value as $name => $item) {
                if ($this->isIterable($item)) {
                    $value[$name] = $this->arrayToValues($item);
                }
            }
        }
        
        return $value;
    }

    /**
     * @param array $default
     * @param array $data
     * @return array
     */
    protected function arrayMergeDefault(array $default, array $data)
    {
        foreach ($default as $name => $value) {
            if (is_array($value)) {
                $value_data = isset($data[$name]) ? $data[$name] : [];
                $data[$name] = $this->arrayMergeDefault($default[$name], (array) $value_data);
            }
            else {
                if (isset($data[$name]) === false) {
                    $data[$name] = $default[$name];
                }
            }
        }
        return $data;
    }

    /**
     * @param array $filter
     * @param array $data
     * @param bool $keep 
     * @return array
     */
    protected function arrayFilterKeys(array $filter, array $data, $keep)
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $filter) === false && $keep === false) {
                unset($data[$name]);
            }
        }
        return $data;
    }

    /**
     * @param array $keys_to_keep
     * @param array $data
     * @return array
     */
    protected function arrayKeep(array $keys_to_keep, array $data)
    {
        return $this->arrayFilterKeys($keys_to_keep, $data, true);
    }

    /**
     * @param array $keys_to_remove
     * @param array $data
     * @return array
     */
    protected function arrayRemove(array $keys_to_remove, array $data)
    {
        return $this->arrayFilterKeys($keys_to_remove, $data, false);
    }

    /**
     * Input
     *
     * [
     *  'Foo.bar' => 1,
     *  'test' => 2,
     * ]
     *
     * Will be converted into
     *
     * [
     *  'Foo.bar' => 1,
     *  '<default_name>.test' => 2,
     * ]
     *
     * 
     * @param array $input
     * @param string $default_name
     * @return array
     */
    protected function arrayDotKeysToScope(array $input, $default_name)
    {
        $data = [];
        foreach($input as $key => $value) {
            $tokens = explode('.', $key);
            @list($name, $property) = $tokens;
            if ($property === null) {
                if ($this->isIterable($value) && empty($value) === false) {
                    foreach ($value as $value_name => $value_value) {
                        //$data["$name.$value_name"] = $value_value;
                    }
                }
                else {
                    $data["$default_name.$name"] = $value;
                }
            }
            else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Input
     *
     * [
     *  'Foo.bar' => 1,
     *  'Foo.test' => 2,
     * ]
     * 
     * Will be converted into
     * 
     * [
     *  'Foo' => [
     *      'bar' => 1.
     *      'test' => 2
     *      ]
     * ]
     * 
     * @param array $data
     * @param string $dot
     * @return array
     */
    protected function arrayDotKeysToArray(array $data, $dot='.')
    {
        $result = [];
        foreach($data as $key => $value) {
            $tokens = explode($dot, $key);
            list($scope_name, $property) = $tokens;
            $result[$scope_name][$property] = $value;
            unset($result[$key]);
        }
        return $result;
    }

    /**
     * Input 
     * ['Foo' => [
     *  'bar' => 1.
     *  'test' => 2 
     * ]]
     * 
     * Will be converted into
     * 
     * [
     *  'Foo.bar' => 1,
     *  'Foo.test' => 2,
     * ]
     * 
     * @param array $data
     * @return array
     */
    protected function arrayDotKeysFlattern(array $data)
    {
        foreach ($data as $name => $values) {
            if ($this->isIterable($values)) {
                foreach ($values as $key => $key_value) {
                    $data["$name.$key"] = $values[$key];
                }
                unset($data[$name]);
            }
        }
        return $data;
    }

    /**
     * @param $key
     * @param $data
     * @return array
     */
    protected function arrayArrangeByKey($key, array $data)
    {
        $result = [];
        for ($a=0; $a<count($data); $a++) {
            $item = $data[$a];
            $result[$item[$key]][] = $item;
        }
        return $result;
    }
    
    /**
     * @param $key
     * @param $data
     * @return array
     */
    protected function arrayArrangeByKeySingle($key, array $data)
    {
        $result = [];
        for ($a=0; $a<count($data); $a++) {
            $item = $data[$a];
            $result[$item[$key]] = $item;
        }
        return $result;
    }
    
    protected function arrayPrefixKey($prefix, array $data, $postfix='')
    {
        foreach ($data as $index => $value) {
            $data[$prefix.$index.$postfix] = $value;
            unset($data[$index]);
        }
        return $data;
    }
}