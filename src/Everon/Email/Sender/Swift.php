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
    public function send(Interfaces\Message $Email)
    {
            $Transport = \Swift_SmtpTransport::newInstance($this->getCredential()->getHost(), $this->getCredential()->getPort(), $this->getCredential()->getEncryption());
            $Transport->setUsername($this->getCredential()->getUsername());
            $Transport->setPassword($this->getCredential()->getPassword());

            /**
             * @var \Swift_Message $Message
             */
            $Message = \Swift_Message::newInstance($Email->getSubject())
                ->setFrom([$this->getCredential()->getEmail() => $this->getCredential()->getName()])
                ->setTo($Email->getRecipient()->getTo())
                ->setBody($Email->getBody());
    //        foreach($Email->getAttachments() as $attachment) {
    //            $Message->attach(\Swift_Attachment::fromPath($attachment));
    //        }
    //        if(!empty($Email->getRecipient()->getCc())) {
    //            $Message->setCc($Email->getRecipient()->getCc()[0]);
    //        }
    //        if(!empty($Email->getRecipient()->getBcc())) {
    //            $Message->setBcc($Email->getRecipient()->getBcc()[0]);
    //        }

            $Mailer = \Swift_Mailer::newInstance($Transport);
    //        sd($Transport);
            $result = $Mailer->send($Message);
            sd($result);
            return $result > 0;

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