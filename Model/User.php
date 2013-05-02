<?php
namespace Everon\Model;


class User
{
    public function getUser($username)
    {
        $data = [
            'id' => 1,
            'username' => $username,
            'password' => 'easy'
        ];

        return new \Everon\Helper\Popo($data);
    }

    public function authenticate($username, $password)
    {
        $User = $this->getUser($username);
        if ($User->getPassword() === $password) {
            return $User;
        }

        return null;
    }    
}
