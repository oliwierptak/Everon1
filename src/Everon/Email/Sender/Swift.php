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
class Swift implements Interfaces\Sender
{
    /**
     * @var Interfaces\Credential
     */
    protected $Credential = null;

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
    function send(Interfaces\Email $Email)
    {
        $Transport = \Swift_SmtpTransport::newInstance($this->getCredential()->getHost(), 25)
            ->setUsername($this->getCredential()->getUsername())
            ->setPassword($this->getCredential()->getPassword());

        $Mailer = \Swift_Mailer::newInstance($Transport);

        $message = \Swift_Message::newInstance($Email->getSubject())
            ->setFrom([$this->getCredential()->getEmail() => $this->getCredential()->getName()])
            ->setTo($Email->getRecipient()->getTo())
            ->setBody($Email->getBody());

        return $Mailer->send($message) > 0;
    }

    /**
     * @param Interfaces\Credential $Credential
     */
    public function setCredential(Interfaces\Credential $Credential)
    {
        $this->Credential = $Credential;
    }

    /**
     * @return Interfaces\Credential
     */
    public function getCredential()
    {
        return $this->Credential;
    }
    
}