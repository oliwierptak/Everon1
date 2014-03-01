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
     * @param bool $deep
     * @return array
     */
    public function toArray($deep=false)
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
        
        if ($deep) {
            foreach ($data as $key => $value) {
                if (is_object($value) && method_exists($value, 'toArray')) {
                    $data[$key] = $value->toArray($deep);
                }
            }
        }
        
        return $data;
    }

}
