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
     * @var Interfaces\Credential
     */
    protected $Credential;

    /**
     * @param Interfaces\Credential $Credentials
     */
    public function __construct(Interfaces\Credential $Credentials)
    {
        $this->Credential = $Credentials;
    }

    /**
     * @inheritdoc
     */
    function send(Interfaces\Message $Email)
    {
        $this->appendCcAndBccToHeaders($Email);
        mail ($Email->getRecipient()->getTo(), $Email->getSubject(), $Email->getBody(), $Email->getHeaders());
    }

    private function appendCcAndBccToHeaders(Interfaces\Message $Email)
    {
        $headers = $Email->getHeaders();
        foreach ($Email->getRecipient()->getCc() as $cc) {
            $headers .= 'Cc: '.$cc."\r\n";
        }
        foreach ($Email->getRecipient()->getBcc() as $bcc) {
            $headers .= 'Bcc: '.$bcc."\r\n";
        }
    }

    /**
     * @param Interfaces\Credential $Credential
     */
    function setCredential(Interfaces\Credential $Credential)
    {
        $this->Credential = $Credential;
    }

    /**
     * @return Interfaces\Credential
     */
    function getCredential()
    {
        return $this->Credential;
    }


} 