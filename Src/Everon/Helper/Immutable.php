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

use Everon\Exception;

trait Immutable
{
    protected $isStateLocked = false;
    
    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($this->isStateLocked) {
            throw new Exception\Core('Immutable object, unable to set value: "%s"', $name);
        }
        
        $this->$name = $value;
    }

    /**
     * After locking, setting any property will throw exception
     */
    protected function lock()
    {
        $this->isStateLocked = true;
    }
}
