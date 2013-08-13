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


trait ArrayFilterKeys
{

    /**
     * @param array $keys
     * @param array $data
     * @param bool $keep
     * @return array
     */
    public function arrayFilterKeys(array $keys, array $data, $keep)
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $keys) === $keep) {
                unset($data[$name]);
            }
        }

        return $data;
    }

}
