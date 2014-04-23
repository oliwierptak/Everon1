<?php
/**
 * Created by PhpStorm.
 * User: Zeger
 * Date: 4/21/14
 * Time: 9:36 PM
 */
namespace Everon\Email\Interfaces;

interface Manager
{
    public function send(Sender $Sender, Email $Email, array $receivers);
}