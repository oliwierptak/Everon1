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
    public function send(Interfaces\Message $Message)
    {
        $Transport = \Swift_SmtpTransport::newInstance($this->getCredential()->getHost(), $this->getCredential()->getPort(), $this->getCredential()->getEncryption());
        $Transport->setUsername($this->getCredential()->getUsername());
        $Transport->setPassword($this->getCredential()->getPassword());
        /**
         * @var \Swift_Message $SwiftMessage
         */
        $SwiftMessage = \Swift_Message::newInstance($Message->getSubject())
            ->setFrom([$Message->getFromEmail() => $Message->getFromName()])
            ->setTo($Message->getRecipient()->getTo(), $Message->getRecipient()->getName())
            ->setBody($Message->getRichBody(), 'text/html');
        $SwiftMessage->addPart($Message->getPlainBody(), 'text/plain');

        foreach($Message->getAttachments() as $name => $attachment) {
            $SwiftMessage->attach(\Swift_Attachment::fromPath($attachment)
                ->setFilename($name));
        }
        if(!empty($Message->getRecipient()->getCc())) {
            $SwiftMessage->setCc($Message->getRecipient()->getCc());
        }
        if(!empty($Message->getRecipient()->getBcc())) {
            $SwiftMessage->setBcc($Message->getRecipient()->getBcc());
        }

        $Mailer = \Swift_Mailer::newInstance($Transport);
        $result = $Mailer->send($SwiftMessage);
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