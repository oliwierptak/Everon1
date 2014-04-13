<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon;

class RequestIdentifier
{
    protected $guid;
    
    protected $system_memory_at_start = null;
    
    
    public function __construct()
    {
        $this->guid = md5(uniqid());
    }
    
    public function getValue()
    {
        return $this->guid;
    }
    
    public function getSystemMemoryAtStart()
    {
        return $this->system_memory_at_start;
    }
    
    public function setSystemMemoryAtStart($value)
    {
        $this->system_memory_at_start = $value;
    }
    
    public function getStats()
    {
        return [
            'memory_at_start' => $this->system_memory_at_start,
            'memory_total' => (memory_get_usage(true)),
            'time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
        ];
    }
    
    public function __toString()
    {
        return $this->guid;
    }

    public function __sleep()
    {
        //todo: test me xxx
        return [
            'guid',
            'system_memory_at_start',
        ];
    }

    public static function __set_state(array $array)
    {
        //todo: test me xxx
        $RequestIdentifier = new static($array);
        $RequestIdentifier->guid = $array['guid'];
        $RequestIdentifier->system_memory_at_start = $array['system_memory_at_start'];
    }
    
}