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
                $data[$name] = $this->arrayMergeDefault($default[$name], $value_data);
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
            if (in_array($name, $filter) === $keep) {
                unset($data[$name]);
            }
        }

        return $data;
    }

    /**
     * @param array $filter
     * @param array $data
     * @return array
     */
    protected function arrayKeep(array $filter, array $data)
    {
        return $this->arrayFilterKeys($filter, $data, true);
    }

    /**
     * @param array $filter
     * @param array $data
     * @return array
     */
    protected function arrayRemove(array $filter, array $data)
    {
        return $this->arrayFilterKeys($filter, $data, false);
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
     * @param $input
     * @param string $default_name 'View' is the default
     * @return array
     */
    protected function arrayDotKeysToScope($input, $default_name='View')
    {
        $data = [];
        foreach($input as $key => $value) {
            $tokens = explode('.', $key);
            @list($name, $property) = $tokens;
            if ($property === null) {
                $data["$default_name.$name"] = $value;
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
     * @param $data
     * @return array
     */
    protected function arrayDotKeysToArray($data)
    {
        $result = [];
        foreach($data as $key => $value) {
            $tokens = explode('.', $key);
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
}