<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Everon\Email\Send;


use Everon\Email\Interfaces\Email;
use Everon\Email\Interfaces\Sender;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class SwiftMailer implements Sender
{

    protected $username;

    protected $password;

    protected $server;

    protected $port;

    protected $fromEmail;

    protected $senderName;

    public function __construct($password, $username, $server, $port, $fromEmail, $senderName)
    {
        $this->password = $password;
        $this->username = $username;
        $this->server = $server;
        $this->port = $port;
        $this->fromEmail = $fromEmail;
        $this->senderName = $senderName;
    }

    /**
     * @inheritdoc
     */
    function send(Email $Email, $receiver)
    {
        $transport = \Swift_SmtpTransport::newInstance($this->server, 25)
            ->setUsername($this->username)
            ->setPassword($this->password);

        $mailer = \Swift_Mailer::newInstance($transport);

        $message = \Swift_Message::newInstance($Email->getSubject())
            ->setFrom(array($this->fromEmail => $this->senderName))
            ->setTo(array($receiver))
            ->setBody($Email->getMessage());

        //set headers
        return $mailer->send($message);
    }

}