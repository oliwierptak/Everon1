<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 8:57 PM
 */

namespace Everon\Email\Interfaces;


interface Sender {

    /**
     * @param Email $Email
     * @param array $receiver
     * @return bool
     */
    function send(Email $Email, $receiver);
} 