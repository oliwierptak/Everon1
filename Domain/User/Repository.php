<?php
namespace Everon\Domain\User;

use Everon\Domain;
use Everon\Helper;

class Repository extends Domain\Repository
{
    /**
     * @param $username
     * @return Entity
     */
    public function getUserByUsername($username)
    {
        $data = [ //comes from Mapper
            'id' => 1,
            'username' => $username,
            'password' => 'easy'
        ];
        
        //$this->getMapper()->fetchOne(1);

        return new Domain\User\Entity($data);
    }
}
