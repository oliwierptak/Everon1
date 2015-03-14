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
     * @param \Exception $Previous
     */
    public function __construct(Interfaces\Message $HttpMessage, \Exception $Previous=null)
    {
        $this->HttpMessage = $HttpMessage;
        if ($Previous !== null && $Previous->getMessage() === $HttpMessage->getInfo()) {
            parent::__construct(null, $params=null, $Previous, $Callback=null);
        }
        else {
            parent::__construct($HttpMessage->getInfo(), $params=null, $Previous, $Callback=null);
        }
    }

    /**
     * @return Interfaces\Message
     */
    public function getHttpMessage()
    {
        return $this->HttpMessage;
    }
}