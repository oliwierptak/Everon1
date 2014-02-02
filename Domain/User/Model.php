<?php
namespace Everon\Domain\User;

use Everon\Dependency;
use Everon\Helper;

class Model
{
    use Dependency\Injection\Logger;
    use Dependency\Injection\DomainManager;


    /**
     * Example
     * 
     * @param $username
     * @param $password
     * @return \Everon\Helper\Popo|null
     */
    public function authenticate($username, $password)
    {
        $User = $this->getDomainManager()->getUserRepository()->getByLogin($username);
        sd($User);
        if ($User->getPassword() === $password) {
            $this->getLogger()->auth('Authentication successful for user: "%s"', [$username]);
            return $User;
        }

        return null;
    }
 
}
