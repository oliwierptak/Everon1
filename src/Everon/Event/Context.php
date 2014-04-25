<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Event;

use Everon\Helper;
use Everon\Event\Dependency;

class Context implements Interfaces\Context
{
    use Dependency\Injection\EventManager;
    
    /**
     * @var \Closure
     */
    protected $Callback = null;
    
    
    public function __construct(\Closure $Callback)
    {
        $this->Callback = $Callback->bindTo($this);
    }
    
    public function __invoke()
    {
        return $this->Callback->__invoke();
    }
} 