<?php
namespace Everon\Domain\User;

use Everon\Domain;
use Everon\Helper;

/**
 * @method \Everon\DataMapper\Interfaces\User getMapper()
 */
class Repository extends Domain\Repository
{
    
    /**'
     * @param $username
     * @return mixed
     */
    public function getByLogin($username)
    {
        $Criteria = new \Everon\DataMapper\Criteria();  
        $Criteria->where(['login' => $username]);

        $data = $this->getMapper()->fetchAll($Criteria);
        if (empty($data)) {
            return null;
        }

        $data = current($data);
        $id = $this->getMapper()->getAndValidateId($data);

        return $this->getDomainManager()->getEntity($this->getName(), $id, $data);
    }
}
