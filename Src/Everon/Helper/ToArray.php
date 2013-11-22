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
    use IsIterable;

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
        $data = (property_exists($this, 'data')) ? $this->data : [];
        
        if ($this->isIterable($data) === false) {
            if (method_exists($this, 'getToArray')) {
                $data = $this->getToArray();
            }
        }

        if ($this->isIterable($data) === false) {
            return [];
        }
        
        foreach ($data as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $data[$key] = $value->toArray();
            }
        }
        
        return $data;
    }

}
