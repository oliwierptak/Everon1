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

trait Decorator
{
    use IsCallable;

    protected $Decoratee = null;

    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $Decoratee = $this->Decoratee;
        if ($this->isCallable($Decoratee, $name)) {
            return call_user_func_array([$Decoratee, $name], $arguments);
        }

        return call_user_func_array([$this, $name], $arguments);
    }
}