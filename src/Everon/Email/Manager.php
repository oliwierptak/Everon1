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

use Everon\Email\Interfaces;
use Everon\Dependency;

/**
 * @author Zeger Hoogeboom <zeger_hoogeboom@hotmail.com>
 */
class Manager implements Interfaces\Manager
{
    use Dependency\Injection\Logger;

    /**
     * @inheritdoc
     */
    public function send(Interfaces\Sender $Sender, Interfaces\Email $Email, Interfaces\Recipient $Recipient)
    {
        try {
            return $Sender->send($Email, $Recipient);
        }
        catch (\Exception $e) {
            $this->getLogger()->email($e);
            return false;
        }
    }
} 