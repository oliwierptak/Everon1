<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Rest;

use Everon\Exception;
use Everon\Helper;
use Everon\Rest\Interfaces;

//todo: wny not merge with the Response?
//todo Response should kinda become Http\Response
class Response extends \Everon\Response implements Interfaces\Response
{
    public function isError()
    {
        return $this->isClientError() || $this->isServerError();
    }

    public function isClientError()
    {
        return $this->status_code >= 400 && $this->status_code < 500;
    }    
    
    public function isServerError()
    {
        return $this->status_code >= 500 && $this->status_code < 600;
    }
}
