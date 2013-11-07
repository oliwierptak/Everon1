<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http;

use Everon\Exception as EveronException; 
use Everon\Interfaces;

class Exception extends EveronException
{
    protected $http_status = null;
    protected $http_message = null;
    
    /**
     * @param string|\Exception $message
     * @param null|array $params
     * @param null|\Exception $Previous
     */
    public function __construct($message, $params=null, $Previous=null) 
    {
        $SendHttpHeaders = function(){
            $this->send();
        };
        
        parent::__construct($message, $params, $Previous, $SendHttpHeaders);
    }
    
    protected function send() //todo: use response
    {
        header(vsprintf("HTTP/1.1 %d %s", [$this->http_status, $this->http_message]));
    }
}