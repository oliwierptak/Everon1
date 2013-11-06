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

class Guid
{
    protected $guid;
    
    public function __construct()
    {
        $this->guid = md5(uniqid());
    }
    
    public function getValue()
    {
        return $this->guid;
    }
    
    public function __toString()
    {
        return $this->guid;
    }
}