<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 8:57 PM
 */

namespace Everon\Email\Send;


use Everon\Email\Interfaces\Email;
use Everon\Email\Interfaces\Sender;

class SwiftMailer implements Sender
{

    protected $username;

    protected $password;

    function __construct($password, $username)
    {
        $this->password = $password;
        $this->username = $username;
    }

    /**
     * @inheritdoc
     */
    function send(Email $Email, $receiver)
    {
        $transport = Swift_SmtpTransport::newInstance('smtp.example.org', 25)
            ->setUsername($this->username)
            ->setPassword($this->password);

        $mailer = Swift_Mailer::newInstance($transport);

        $message = Swift_Message::newInstance($Email->getSubject())
            ->setFrom(array($this->username.'@grofas.com' => 'Grofas'))
            ->setTo(array($receiver))
            ->setBody($Email->getMessage());

        //set headers
        $result = $mailer->send($message);
    }

} 