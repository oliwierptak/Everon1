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


trait ToArray
{

    /**
     * @var array
     */
    protected $data = [];


    /**
     * array|stdClass $this->data is declared in class which uses this trait
     *
     * @return array
     */
    public function toArray()
    {
        $data = $this->data;
        if (method_exists($this, 'getData')) { //in case of closures or what not
            $data = $this->getData();
        }
        
        foreach ($data as $key => $value ) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $data[$key] = $value->toArray();
            }
        }
        
        return $data;
    }

}
