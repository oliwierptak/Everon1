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


use Everon\Email\Interfaces\Email;
use Everon\Email\Interfaces\Sender;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class PHPMailer implements Sender{

    /**
     * @inheritdoc
     */
    function send(Email $Email, $receiver)
    {
        mail($receiver,$Email->getSubject(),$Email->getMessage());
    }

} 