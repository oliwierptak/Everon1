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
        return (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
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

    protected function arrayKeep(array $filter, array $data)
    {
        return $this->arrayFilterKeys($filter, $data, true);
    }

    protected function arrayRemove(array $filter, array $data)
    {
        return $this->arrayFilterKeys($filter, $data, false);
    }

}