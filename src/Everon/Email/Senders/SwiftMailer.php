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

class SwiftMailer implements Sender{

    /**
     * @inheritdoc
     */
    function send(Email $Email, $receiver)
    {
    }

} 