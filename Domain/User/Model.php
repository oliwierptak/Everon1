<?php
namespace Everon\Domain\User;

use Everon\Dependency;
use Everon\Domain\Exception;
use Everon\Helper;

class Model
{
    use Dependency\Injection\Logger;
    use Dependency\Injection\DomainManager;


    /**
     * @param $email
     * @param $password
     * @return \Everon\Domain\User\Entity|null
     * @throws \Everon\Domain\Exception\Model
     */
    public function authenticate($email, $password)
    {
        $User = $this->getDomainManager()->getUserRepository()->getByEmail($email);
        sd($User);
        if ($User === null) {
            return null;
        }
        
        if ($User->getPassword() === $password) {
            $this->getLogger()->auth('Authentication successful for: "%s"', [$email]);
            return $User;
        }
    }
 
}
