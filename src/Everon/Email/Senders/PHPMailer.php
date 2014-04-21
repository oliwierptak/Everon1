<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 8:58 PM
 */

namespace Everon\Email\Send;


use Everon\Email\Interfaces\Email;
use Everon\Email\Interfaces\Sender;

class PHPMailer implements Sender{

    /**
     * @inheritdoc
     */
    function send(Email $Email, $receiver)
    {
        mail($receiver,$Email->getSubject(),$Email->getMessage());
    }

} 