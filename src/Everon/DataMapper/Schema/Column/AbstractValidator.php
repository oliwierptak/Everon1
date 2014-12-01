<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\DataMapper\Schema\Column;

use Everon\Dependency;
use Everon\DataMapper\Interfaces;

abstract class AbstractValidator implements Interfaces\Schema\Column\Validator 
{
    use Dependency\Injection\Factory;
    
    /**
     * @inheritdoc
     */
    abstract public function validateValue($value);
    
    public function __sleep()
    {
        $vars = get_object_vars($this);
        unset($vars['Factory']);
        $data = array_keys($vars);
        return $data;
    }

    public function __wakeup()
    {
        $this->Factory = @$GLOBALS['EVERON_FACTORY']; //xxx meh
    }
}