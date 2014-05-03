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

class Exception extends EveronException implements Interfaces\Exception
{
    /**
     * @var Interfaces\Message $Message
     */
    protected $HttpMessage;

    /**
     * @param Interfaces\Message $HttpMessage
     */
    public function __construct(Interfaces\Message $HttpMessage)
    {
        $this->HttpMessage = $HttpMessage;
        parent::__construct($HttpMessage->getInfo(), $params=null, $Previous=null, $Callback=null);
    }

    /**
     * @return Interfaces\Message
     */
    public function getHttpMessage()
    {
        return $this->HttpMessage;
    }
}