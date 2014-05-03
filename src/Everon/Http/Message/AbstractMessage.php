<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Http\Message;

use Everon\Helper;
use Everon\Http\Interfaces;

class AbstractMessage implements Interfaces\Message
{
    use Helper\Immutable;
    
    /**
     * @var int
     */
    protected $http_status_code = null;

    /**
     * @var string
     */
    protected $http_message = null;
    
    protected $info = null;
    
    
    public function __construct($info=null)
    {
        $this->info = $info;
        $this->lock();
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->http_message;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->http_status_code;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }
}