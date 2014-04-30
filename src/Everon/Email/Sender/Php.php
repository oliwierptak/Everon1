<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email\Sender;

use Everon\Email\Interfaces;


/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 * @author Oliwier Ptak <oliwierptak@gmail.com>
 */
class Php implements Interfaces\Sender
{

    /**
     * @inheritdoc
     */
    function send(Interfaces\Message $Email, Interfaces\Recipient $Recipient)
    {
        $this->appendCcAndBccToHeaders($Email, $Recipient);
        mail ($Recipient->getTo(), $Email->getSubject(), $Email->getBody(), $Email->getHeaders());
    }

    private function appendCcAndBccToHeaders(Interfaces\Message $Email, Interfaces\Recipient $Recipient)
    {
        $headers = $Email->getHeaders();
        foreach ($Recipient->getCc() as $cc) {
            $headers .= 'Cc: '.$cc."\r\n";
        }
        foreach ($Recipient->getBcc() as $bcc) {
            $headers .= 'Bcc: '.$bcc."\r\n";
        }
    }

} 