<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 8:52 PM
 */

namespace Everon\Email;


use Everon\Email\Interfaces\Email;
use Everon\Email\Interfaces\Sender;

class Manager implements \Everon\Email\Interfaces\Manager
{

    public function send(Sender $Sender, Email $Email, array $receivers)
    {
        foreach ($receivers as $receiver)
        {
            $Sender->send($Email, $receiver);
        }
    }
} 