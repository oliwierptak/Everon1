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


/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Context implements Interfaces\Context
{
    use Dependency\Injection\EventManager;
    
    /**
     * @var \Closure
     */
    protected $Callback = null;
    
    protected $Caller = null;


    /**
     * @param callable $Callback
     * @param $Scope
     */
    public function __construct(\Closure $Callback, $Scope)
    {
        $this->Callback = $Callback->bindTo($Scope);
    }
        
    public function __invoke()
    {
        return $this->execute();
    }

    /**
     * @inheritdoc
     */
    public function setCallback(\Closure $Callback)
    {
        $this->Callback = $Callback;
    }

    /**
     * @inheritdoc
     */
    public function getCallback()
    {
        return $this->Callback;
    }

    /**
     * @inheritdoc
     */
    public function setCaller($Callee)
    {
        $this->Caller = $Callee;
    }

    /**
     * @inheritdoc
     */
    public function getCaller()
    {
        return $this->Caller;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        return call_user_func($this->Callback, $this->Caller);
    }

} 