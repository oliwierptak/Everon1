<?php
namespace Everon\Domain\User;

use Everon\Dependency;
use Everon\Helper;

class Model
{
    use Dependency\Injection\Logger;
    
    /**
     * Example
     * 
     * @param $username
     * @return Helper\Popo
     */
    public function getUser($username)
    {
        $data = [
            'id' => 1,
            'username' => $username,
            'password' => 'easy'
        ];

        return new Helper\Popo($data);
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
            $this->getLogger()->auth('Authentication successful for user: "%s"', [$username]);
            return $User;
        }

        return null;
    }
 
}
