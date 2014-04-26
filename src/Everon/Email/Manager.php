<?php
/**
 * This file is part of the Everon framework.
 *
 * (c) Oliwier Ptak <oliwierptak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Everon\Email;


use Everon\Email\Interfaces\Email;
use Everon\Email\Interfaces\Sender;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
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