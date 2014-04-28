<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everon\Email\Senders;

use Everon\Email\Interfaces;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class SwiftMailer implements Interfaces\Sender
{
    protected $Credentials;

    public function __construct(Interfaces\Credential $Credentials)
    {
        $this->Credentials = $Credentials;
    }

    /**
     * @inheritdoc
     */
    function send(Interfaces\Email $Email, Interfaces\Recipient $Recipient)
    {
        $transport = \Swift_SmtpTransport::newInstance($this->Credentials->getServer(), 25)
            ->setUsername($this->username)
            ->setPassword($this->password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance($Email->getSubject())
            ->setFrom([$this->Credentials->getSenderEmail() => $this->senderName])
            ->setTo($Recipient->getTo())
            ->setBody($Email->getMessage());

        return $mailer->send($message) > 0;
    }

}