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

    public function getHttpMessage()
    {
        return $this->http_message;
    }
    
    public function getHttpStatus()
    {
        return $this->http_status;
    }
}