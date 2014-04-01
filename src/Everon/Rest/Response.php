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
use Everon\Http\HeaderCollection;
use Everon\Rest\Interfaces;

class Response extends \Everon\Http\Response implements Interfaces\Response
{    
    /**
     * @return bool
     */
    public function isError()
    {
        return $this->isClientError() || $this->isServerError();
    }

    /**
     * @return bool
     */
    public function isClientError()
    {
        return $this->status_code >= 400 && $this->status_code < 500;
    }

    /**
     * @return bool
     */
    public function isServerError()
    {
        return $this->status_code >= 500 && $this->status_code < 600;
    }
}
