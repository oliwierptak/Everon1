<?php
namespace Everon\Model;

use Everon\Dependency;
use Everon\Exception;
use Everon\Interfaces;

class User
{
    use Dependency\Injection\Logger;

    /**
     * Example
     * 
     * @param $username
     * @return \Everon\Helper\Popo
     */
    public function getUser($username)
    {
        $data = [
            'id' => 1,
            'username' => $username,
            'password' => 'easy'
        ];

        return new \Everon\Helper\Popo($data);
    }

    /**
     * Example
     * 
     * @param $username
     * @param $password
     * @return \Everon\Helper\Popo|null
     */
    public function authenticate($username, $password)
    {
        $User = $this->getUser($username);
        if ($User->getPassword() === $password) {
            return $User;
        }

        $this->getLogger()->warn('Failed login attempt for: "%s"', [$username]);
        return null;
    }
 
}
