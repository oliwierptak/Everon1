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


trait ArrayMergeDefault
{

    public function arrayMergeDefault(array $default, array $data)
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

}
