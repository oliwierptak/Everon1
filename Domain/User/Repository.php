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
     * @param $email
     * @return mixed
     */
    public function getByEmail($email)
    {
        $Criteria = new \Everon\DataMapper\Criteria();  
        $Criteria->where(['email' => $email]);

        $data = $this->getMapper()->fetchAll($Criteria);
        if (empty($data)) {
            return null;
        }

        $data = current($data);
        $id = $this->getMapper()->getAndValidateId($data);

        return $this->getDomainManager()->getEntity($this->getName(), $id, $data);
    }
}
